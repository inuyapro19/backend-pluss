<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesChartController extends Controller
{
    public function getMonthlySalesData()
    {
        $year = \request('year');
        $salesData = Order::selectRaw('MONTHNAME(fecha_compra) as month, SUM(total) as total')
            ->whereYear('fecha_compra', $year)
            ->where('estado', 0)
            ->groupByRaw('MONTH(fecha_compra)')
            ->orderByRaw('MONTH(fecha_compra)')
            ->get();


        return response()->json($salesData);
    }


    //grafico para ventas por dia del mes actual
    public function getDailySalesData()
    {
        setlocale(LC_TIME, 'es_ES'); // Configurar el idioma para español
        $currentYear = date('Y'); // Año actual
        $currentMonth = date('m'); // Mes actual

        $salesData = Order::selectRaw('DAY(fecha_compra) as day, SUM(total) as total')
            ->whereYear('fecha_compra', $currentYear)
            ->whereMonth('fecha_compra', $currentMonth)
            ->where('estado', 0)
            ->groupByRaw('DAY(fecha_compra)')
            ->orderByRaw('DAY(fecha_compra)')
            ->get();

        // Formatear la respuesta
        $formattedData = $salesData->map(function ($item) use ($currentYear, $currentMonth) {
            $dateString = $currentYear . '-' . $currentMonth . '-' . $item->day;
            $formattedDate = strftime('%b %d', strtotime($dateString)); // Formato del mes en español
            return [
                'date' => $formattedDate,
                'total' => $item->total
            ];
        });

        return response()->json($formattedData);
    }




}
