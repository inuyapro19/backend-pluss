<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;
    protected $connection = 'ticket_mysql';
    protected $table = 'orders';

    protected $fillable = [
        'number_order',
        'access_token',
        'api_token_compra',
        'token_web_pay',
        'res_web_pay',
        'res_compra_recorridos',
        'fecha_compra',
        'fecha_confimacion_compra',
        'cantidad',
        'total',
        'estado'
    ];

    public $timestamps = false;

    //fecha de comprar en formato fecha y separ hora
    public function getFechaCompraAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }

    //fecha de confirmacion de compra en formato fecha y separ hora
    public function getFechaConfirmacionCompraAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }


    //relaciones
    public function orderDetails(){
        return $this->hasMany(OrderDetail::class);
    }

    //relacion con cliente
    public function cliente(){
        return $this->hasMany(Cliente::class);
    }


    //query scope
    public function scopeFilterFechas($query, $filters)
    {
        if (isset($filters['fecha_inicio']) && isset($filters['fecha_fin'])) {
            $query->whereBetween('fecha_compra', [$filters['fecha_inicio'], $filters['fecha_fin']]);
        }
    }

    public function scopeFiltros(Builder $query){
        if (empty(request('filtro'))){
            return;
        }

        $filtros = request('filtro');

      foreach($filtros as $filtro => $value){
          $query->where($filtro, $value);
      }

    }

    //formateo de precio peso chileno
    public function getTotalAttribute($value)
    {
        return '$ '.number_format($value, 0, ',', '.');
    }

    //formateo de estado de la orden
    public function getEstadoAttribute($value)
    {
        if($value == 0){
            return 'Pagado';
        }else if($value == 1){
            return 'Pendiente';
        }else if($value == 2){
            return 'Anulado';
        }
    }


}
