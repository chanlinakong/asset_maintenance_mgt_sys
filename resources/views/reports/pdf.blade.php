<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <title>
        Asset Maintenance Report
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        h1 {
            margin-bottom: 5px;
        }

        h2 {
            margin-top: 25px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 20px;
        }

        .summary {
            width: 100%;
            margin-bottom: 25px;
        }

        .summary td {
            width: 25%;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .label {
            color: #666;
            font-size: 10px;
        }

        .value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 7px;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            color: #777;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <h1>
        Asset Maintenance Report
    </h1>

    <div class="subtitle">

        @if($from || $to)

            Period:
            {{ $from ?? 'Beginning' }}
            -
            {{ $to ?? 'Present' }}

        @else

            All maintenance records

        @endif

    </div>


    {{-- Summary --}}

    <table class="summary">

        <tr>

            <td>
                <div class="label">
                    Maintenance Records
                </div>

                <div class="value">
                    {{ $totalRecords }}
                </div>
            </td>

            <td>
                <div class="label">
                    Total Cost
                </div>

                <div class="value">
                    ${{ number_format($totalCost, 2) }}
                </div>
            </td>

            <td>
                <div class="label">
                    Completed
                </div>

                <div class="value">
                    {{ $completedCount }}
                </div>
            </td>

            <td>
                <div class="label">
                    In Progress
                </div>

                <div class="value">
                    {{ $inProgressCount }}
                </div>
            </td>

        </tr>

    </table>


    {{-- Cost by vehicle --}}

    <h2>
        Maintenance Cost by Vehicle
    </h2>

    <table>

        <thead>

            <tr>

                <th>
                    Vehicle
                </th>

                <th>
                    Name
                </th>

                <th>
                    Records
                </th>

                <th class="right">
                    Cost
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($costByVehicle as $item)

                <tr>

                    <td>
                        {{ $item['vehicle']->vehicle_code }}
                    </td>

                    <td>
                        {{ $item['vehicle']->name }}
                    </td>

                    <td>
                        {{ $item['count'] }}
                    </td>

                    <td class="right">
                        ${{ number_format($item['cost'], 2) }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="4">
                        No data available.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- Cost by type --}}

    <h2>
        Maintenance by Type
    </h2>

    <table>

        <thead>

            <tr>

                <th>
                    Type
                </th>

                <th>
                    Records
                </th>

                <th class="right">
                    Total Cost
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($costByType as $item)

                    <tr>

                        <td>
                            {{ str($item['type']->value)
                ->replace('_', ' ')
                ->title() }}
                        </td>

                        <td>
                            {{ $item['count'] }}
                        </td>

                        <td class="right">
                            ${{ number_format($item['cost'], 2) }}
                        </td>

                    </tr>

            @empty

                <tr>

                    <td colspan="3">
                        No data available.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- Detailed records --}}

    <h2>
        Maintenance Details
    </h2>

    <table>

        <thead>

            <tr>

                <th>
                    Date
                </th>

                <th>
                    Vehicle
                </th>

                <th>
                    Maintenance
                </th>

                <th>
                    Status
                </th>

                <th class="right">
                    Cost
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($records as $record)

                    <tr>

                        <td>
                            {{ $record->reported_at->format('d M Y') }}
                        </td>

                        <td>
                            {{ $record->vehicle->vehicle_code }}
                        </td>

                        <td>
                            {{ $record->title }}
                        </td>

                        <td>
                            {{ str($record->status->value)
                ->replace('_', ' ')
                ->title() }}
                        </td>

                        <td class="right">
                            ${{ number_format((float) $record->cost, 2) }}
                        </td>

                    </tr>

            @empty

                <tr>

                    <td colspan="5">
                        No maintenance records found.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <div class="footer">

        Generated by Asset Maintenance System

    </div>

</body>

</html>