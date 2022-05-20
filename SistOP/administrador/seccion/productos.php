<?php include("../template/cabecera.php")?>

<?php 

$txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
$txtnombre=(isset($_POST['txtnombre']))?$_POST['txtnombre']:"";
$txtImagen=(isset($_FILES['txtImagen']['name']))?$_FILES['txtImagen']['name']:"";
$accion=(isset($_POST['accion']))?$_POST['accion']:"";


include("../config/bd.php");


switch($accion){

    case "Agregar":

        //INSERT INTO `libros` (`id`, `nombre`, `imagen`) VALUES (NULL, 'libro de php', 'imagen.jpg');
        $sentenciaSQL=$conexion->prepare("INSERT INTO `libros` (nombre, imagen) VALUES (:nombre, :imagen);");
        $sentenciaSQL->bindParam(':nombre',$txtnombre);

        $fecha=new DateTime();
        $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";
    
        $tmpImagen=$_FILES["txtImagen"]["tmp_name"];

        if($tmpImagen!="")
        {
            move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);
        }

        $sentenciaSQL->bindParam(':imagen',$nombreArchivo);
        $sentenciaSQL->execute();
        echo "Boton agregar presionado";
        break;

    case "Modificar":

        $sentenciaSQL=$conexion->prepare("UPDATE libros SET nombre=:nombre WHERE id=:id");
        $sentenciaSQL->bindParam(':nombre', $txtnombre);
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();

        if($txtImagen!=""){

            $fecha=new DateTime();
            $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";
            
            $tmpImagen=$_FILES["txtImagen"]["tmp_name"];
            
            move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);

            $sentenciaSQL=$conexion->prepare("SELECT imagen FROM libros WHERE id=:id");
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();
            $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

            if(isset($libro["imagen"]) && ($libro["imagen"]!="imagen.jpg"))
            {
                if(file_exists("../../img/".$libro["imagen"]))
                {
                    unlink("../../img/".$libro["imagen"]);
                }
            }


            $sentenciaSQL=$conexion->prepare("UPDATE libros SET imagen=:imagen WHERE id=:id");
            $sentenciaSQL->bindParam(':imagen', $nombreArchivo);
            $sentenciaSQL->bindParam(':id', $txtID);
            $sentenciaSQL->execute();
        }

        break;

    case "Cancelar":
        echo "Boton cancelar presionado";
        break;

    case "Seleccionar":
        //echo "Boton seleccionar presionado";
        $sentenciaSQL=$conexion->prepare("SELECT * FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
        $txtnombre=$libro['nombre'];
        $txtImagen=$libro['imagen'];


        break;

    case "Borrar":
       
        $sentenciaSQL=$conexion->prepare("SELECT imagen FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();
        $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

        if(isset($libro["imagen"]) && ($libro["imagen"]!="imagen.jpg"))
        {
            if(file_exists("../../img/".$libro["imagen"]))
            {
                unlink("../../img/".$libro["imagen"]);
            }
        }

         $sentenciaSQL=$conexion->prepare("DELETE FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        break;

}


$sentenciaSQL=$conexion->prepare("SELECT * FROM libros");
$sentenciaSQL->execute();
$listaLibros=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);


?>

<div class="col-md-5">
    
    <div class="card">
        <div class="card-header">
            Datos del libro
        </div>

        <div class="card-body">
            
            <form method="POST" enctype="multipart/form-data">
                <div class = "form-group">
                    <label for="txtID">ID: </label>
                    <input type="text" class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" placeholder="ID del libro">
                </div>
                
                <div class = "form-group">
                    <label for="txtnombre">Nombre: </label>
                    <input type="text" class="form-control" value="<?php echo $txtnombre; ?> "name="txtnombre" id="txtnombre" placeholder="Nombre del libro">
                </div>

                <div class = "form-group">
                    <label for="txtImagen">Imagen: </label>

                    <?php echo $txtImagen?>

                    <?php if($txtImagen!=""){?>

                        <img src="../../img/<?php echo $txtImagen;?>" width="50" alt="" srcset="">

                    <?php } ?>

                    <input type="file" class="form-control" value="<?php echo $txtImagen; ?> " name="txtImagen" id="txtImagen" placeholder="Imagen del libro">
                </div>

                <div class="btn-group" role="group" aria-label="">
                    <button type="submit" name="accion" value="Agregar" class="btn btn-success">Agregar</button>
                    <button type="submit" name="accion" value="Modificar" class="btn btn-warning">Modificar</button>
                    <button type="submit" name="accion" value="Cancelar" class="btn btn-info">Cancelar</button>
                </div>
            
            </form>



        </div>

        
    </div>


</div>

<div class="col-md-7">


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del libro</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php  foreach($listaLibros as $libro) { ?>
            <tr>
                <td><?php echo $libro['id']; ?></td>
                <td><?php echo $libro['nombre']; ?></td>
                <td>
                    
                <img src="../../img/<?php echo $libro['imagen'];?>" width="50" alt="" srcset="">
                
                
                <?php echo $libro['imagen']; ?></td>

                <th>


                <form method="post">

                    <input type="hidden" name="txtID" id="txtID" value="<?php echo $libro['id']; ?>"/>

                    <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary"/>

                    <input type="submit" name="accion" value="Borrar" class="btn btn-danger"/>

                </form>




                </th>

               
            
            </tr>
        <?php } ?>
        </tbody>
    </table>




</div>


<?php include("../template/pie.php")?>
