<?php

namespace app\controllers;
use app\models\mainModel;

class loginController extends mainModel
{

    /*----------  Controlador iniciar sesion  ----------*/
    public function iniciarSesionControlador()
    {

        $usuario = $this->limpiarCadena($_POST['login_usuario']);
        $clave = $this->limpiarCadena($_POST['login_clave']);

        # Verificando campos obligatorios #
        if ($usuario == "" || $clave == "") {
            echo "<script>
			        Swal.fire({
					  icon: 'error',
					  title: 'Ocurrió un error inesperado',
					  text: 'No has llenado todos los campos que son obligatorios'
					});
				</script>";
        } else {

            # Verificando integridad de los datos #
            if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
                echo "<script>
				        Swal.fire({
						  icon: 'error',
						  title: 'Ocurrió un error inesperado',
						  text: 'El USUARIO no coincide con el formato solicitado'
						});
					</script>";
            } else {
                
                # Verificando integridad de los datos #
                if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave)) {
                    echo "<script>
					        Swal.fire({
							  icon: 'error',
							  title: 'Ocurrió un error inesperado',
							  text: 'La CLAVE no coincide con el formato solicitado'
							});
						</script>";
                } else {

                    # Verificando usuario #
                    $check_usuario = $this->ejecutarConsulta("SELECT u.idUsuario AS usuario_id, 
                                                 e.nombre AS usuario_nombre, 
                                                 e.apellido AS usuario_apellido, 
                                                 u.usuario AS usuario_usuario, 
                                                 u.password AS usuario_clave 
                                          FROM usuario u 
                                          INNER JOIN empleados e ON u.idempleado = e.idEmpleado 
                                          WHERE u.usuario = '$usuario'");

                    if ($check_usuario->rowCount() == 1) {

                        $check_usuario = $check_usuario->fetch();

                        if ($check_usuario['usuario_usuario'] == $usuario && password_verify($clave, $check_usuario['usuario_clave'])) {

                            $_SESSION['id'] = $check_usuario['usuario_id'];
                            $_SESSION['nombre'] = $check_usuario['usuario_nombre'];
                            $_SESSION['apellido'] = $check_usuario['usuario_apellido'];
                            $_SESSION['usuario'] = $check_usuario['usuario_usuario'];


                            if (headers_sent()) {
                                echo "<script> window.location.href='" . APP_URL . "dashboard/'; </script>";
                            } else {
                                header("Location: " . APP_URL . "dashboard/");
                            }

                        } else {
                            echo "<script>
							        Swal.fire({
									  icon: 'error',
									  title: 'Ocurrió un error inesperado',
									  text: 'Usuario o clave incorrectos'
									});
								</script>";
                        }

                    } else {
                        echo "<script>
						        Swal.fire({
								  icon: 'error',
								  title: 'Ocurrió un error inesperado',
								  text: 'Usuario o clave incorrectos'
								});
							</script>";
                    }
                }
            }
        }
    }


    /*----------  Controlador cerrar sesion  ----------*/
    public function cerrarSesionControlador()
    {

        session_destroy();

        if (headers_sent()) {
            echo "<script> window.location.href='" . APP_URL . "login/'; </script>";
        } else {
            header("Location: " . APP_URL . "login/");
        }
    }

}