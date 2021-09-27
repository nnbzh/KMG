<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Indicator;
use App\Models\Type;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CompanyIndicatorController extends Controller
{
    use ApiResponse;

    const DATA_INDEX = 0;
    const TYPE_INDEX = 1;
    const DATE_INDEX = 2;
    const COMPANY_NAME_INDEX = 1;
    const QlIQ_START_INDEX = 2;
    const FACT_ID = 1;
    const FORECAST_ID = 2;

    public function import() {
        $this->validate(request(), [
            "file" => "required"
        ]);

        $data = Excel::toArray(null, request()->file('file'))[self::DATA_INDEX] ?? null;

        if (! is_null($data)) {
            $columnCountPerType = $this->getColumnCountForType($data[self::TYPE_INDEX]);
            $datesColumn = array_values(array_filter(array_unique($data[self::DATE_INDEX])));
            $data = collect($this->unsetColumns($data));
            $types = Type::query()->get()->keyBy('slug');
            foreach ($data as $row) {
                $company = Company::query()->where('name', $row[self::COMPANY_NAME_INDEX])->first() ??
                    Company::query()->updateOrCreate(['name' => $row[self::COMPANY_NAME_INDEX]]);

                $counter = 0;
                foreach ($types as $type) {
                    $parsed = array_slice($row, self::QlIQ_START_INDEX);
                    $parsed = array_slice($parsed, $counter * count($datesColumn) * $columnCountPerType, count($parsed) / $columnCountPerType);

                    $dateCounter = 0;
                    foreach ($datesColumn as $date) {
                        $date = Date::excelToDateTimeObject($date)->format('Y-m-d');
                        $indicator = Indicator::query()
                                ->where('company_id', $company->id)
                                ->where('date', $date)
                                ->where('type_id', $type->id)
                                ->first() ?? Indicator::query()->create(
                                [
                                    "type_id" => $type->id,
                                    "company_id" => $company->id,
                                    "date"      => $date
                                ]
                            );

                        $indicator->qliq = $indicator->qliq + $parsed[0];
                        $indicator->qoil = $indicator->qoil + $parsed[$columnCountPerType];
                        $indicator->saveOrFail();
                        unset($parsed[0]);
                        $parsed = array_values($parsed);
                        $dateCounter++;
                    }

                    $counter++;
                }
            }
        }

        return $this->successResponse(true);
    }

    public function getGraphData(): JsonResponse
    {
        $this->validate(request(),
            ['company_id' => "required|exists:companies,id"]
        );

        $data = Company::query()->with('indicators.type')->where('id', request('company_id'))->first();
        $data['indicators'] = $data->indicators->groupBy('type_id');

        return $this->successResponse($data['indicators']);
    }

    /**
     * @param array $data
     * @return array
     */
    private function unsetColumns(array $data): array
    {
        unset(
            $data[self::DATA_INDEX],
            $data[self::DATE_INDEX],
            $data[self::TYPE_INDEX]
        );

        return $data;
    }

    /**
     * @param array $array
     * @return int|string
     */
    private function getColumnCountForType(array $array) {
        $start = 0;
        $end = 0;
        foreach ($array as $index => $row) {
            if ($row === 'Qliq') {
                $start = $index;
            }

            if ($row === "Qoil") {
                $end = $index;
            }
        }

        return $end - $start;
    }
}