<?php
include_once 'Conexion.php';
class Lote{
    var $objetos;
    public function __construct(){
        $db= new Conexion();
        $this->acceso=$db->pdo;
    }
    function crear($id_producto,$proveedor,$stock,$vencimiento){
        $sql="INSERT INTO lote(stock,vencimiento,lote_id_prod,lote_id_prov) values (:stock,:vencimiento,:id_producto,:id_proveedor)";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':stock'=>$stock,'vencimiento'=>$vencimiento,'id_producto'=>$id_producto,'id_proveedor'=>$proveedor));
        echo 'add';
    }
    function buscar(){
        if(!empty($_POST['consulta'])){
            $consulta=$_POST['consulta'];
            $sql="SELECT l.id as id_lote,concat(l.id,' | ',l.codigo) as codigo,cantidad_lote, vencimiento, concentracion, adicional, producto.nombre as prod_nom, tipo_producto.nombre as tip_nom,
            presentacion.nombre as pre_nom, proveedor.nombre as proveedor, producto.avatar as logo
            FROM lote as l
            join compra on l.id_compra = compra.id and l.estado='A'
            join proveedor on proveedor.id_proveedor=compra.id_proveedor
            join producto on producto.id_producto=l.id_producto
            join tipo_producto on prod_tip_prod=id_tip_prod
            join presentacion on prod_present=id_presentacion and producto.nombre like :consulta order by producto.nombre limit 25";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':consulta'=>"%$consulta%"));
            $this->objetos=$query->fetchall();
            return $this->objetos;
        }
        else{
            $sql="SELECT l.id as id_lote,concat(l.id,' | ',l.codigo) as codigo,cantidad_lote, vencimiento, concentracion, adicional, producto.nombre as prod_nom, tipo_producto.nombre as tip_nom,
            presentacion.nombre as pre_nom, proveedor.nombre as proveedor, producto.avatar as logo
            FROM lote as l
            join compra on l.id_compra = compra.id and l.estado='A'
            join proveedor on proveedor.id_proveedor=compra.id_proveedor
            join producto on producto.id_producto=l.id_producto
            join tipo_producto on prod_tip_prod=id_tip_prod
            join presentacion on prod_present=id_presentacion and producto.nombre not like '' order by producto.nombre limit 25";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $this->objetos=$query->fetchall();
            return $this->objetos;
        }
    }
    function editar($id,$stock){
        $sql="UPDATE lote SET cantidad_lote=:stock where id=:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id'=>$id,':stock'=>$stock));
        echo 'edit';
    }
    function borrar($id){
        $sql="UPDATE lote SET estado='I' where id=:id";
        $query=$this->acceso->prepare($sql);
        $query->execute(array(':id'=>$id));
        if(!empty($query->execute(array(':id'=>$id)))){
            echo 'borrado';
        }
        else{
            echo 'noborrado';
        }
    }
    function devolver($id_lote,$cantidad,$vencimiento,$producto,$proveedor){
            $sql="SELECT * FROM lote WHERE id=:id_lote";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_lote'=>$id_lote));
            $lote=$query->fetchall();
         
                $sql="UPDATE lote SET cantidad_lote=cantidad_lote+:cantidad,estado='A' where id=:id_lote";
                $query = $this->acceso->prepare($sql);
                $query->execute(array(':cantidad'=>$cantidad,':id_lote'=>$id_lote));
            
            
    }
    ///////////////////////////////actualizacion//////////////////////////
    function crear_lote($codigo,$cantidad,$vencimiento,$precio_compra,$id_compra,$id_producto){
        $sql="INSERT INTO lote(codigo,cantidad,cantidad_lote,vencimiento,precio_compra,id_compra,id_producto) values (:codigo,:cantidad,:cantidad_lote,:vencimiento,:precio_compra,:id_compra,:id_producto)";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':codigo'=>$codigo,':cantidad'=>$cantidad,':cantidad_lote'=>$cantidad,':vencimiento'=>$vencimiento,':precio_compra'=>$precio_compra,':id_compra'=>$id_compra,':id_producto'=>$id_producto));
        echo 'add';
    }
    function ver($id){
        $sql="SELECT l.codigo as codigo, l.cantidad as cantidad, vencimiento, precio_compra, p.nombre as producto, concentracion,adicional, pre.nombre as presentacion
            FROM lote as l
            join producto as p on l.id_producto=p.id_producto and id_compra=:id
            join tipo_producto as t on prod_tip_prod=id_tip_prod
            join presentacion as pre on prod_present=id_presentacion";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id'=>$id));
            $this->objetos=$query->fetchall();
            return $this->objetos;
    }
    function obtener_stock(){
        $sql="SELECT SUM(l.cantidad_lote) As stock ,
              p.nombre as medicamento,
              p.concentracion as concentracion,
              p.adicional as adicional,
              t.nombre as tipo,
              pre.nombre as presentacion

              FROM lote l
              JOIN producto p ON l.id_producto=p.id_producto 
              join tipo_producto t on p.prod_tip_prod=id_tip_prod
              join presentacion pre on p.prod_present=id_presentacion
             GROUP BY l.id_producto
        ";
        $query=$this->acceso->prepare($sql);
        $query->execute();
        $this->objetos=$query->fetchall();
        return $this->objetos;
    }

}