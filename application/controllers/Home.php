<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->helper("url");//BORRAR CACHÉ DE LA PÁGINA
		$this->load->model('M_Datos');
		$this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
        $this->output->set_header('Pragma: no-cache');
	}

	public function index()
	{
		$this->load->view('v_home');
	}

	function register(){
		$data['error'] = EXIT_ERROR;
      	$data['msj']   = null;
		try {
			$empresa 		= $this->input->post('Company');
			$direccion      = $this->input->post('Direccion');
			$name           = $this->input->post('Name');
			$surname 		= $this->input->post('Surname');
			$cargo 		    = $this->input->post('Position');
			$correo 		= $this->input->post('Email');
			$telefono	    = $this->input->post('Phone');
			$birthday 	    = implode("-", array_reverse(explode("/", $this->input->post('Birthday'))));
			$deporte	    = $this->input->post('Deporte');
			$burger		    = $this->input->post('Burger');
			$existe         = $this->M_Datos->existCorreo($correo);
			$fecha          = date('Y-m-d');
			if(count($existe) != 0) {
				$data['msj']   = 'Correo ya registrado';
			}
			else{
				$insertParticipante = array('empresa'          => $empresa,
											'direccion'        => $direccion,
											'nombre'           => $name,
										   	'apellido'         => $surname,
										   	'cargo'            => $cargo,
										   	'email' 	       => $correo,
										   	'telefono'         => $telefono,
										   	'birthday'         => $birthday,
										   	'deporte'          => $deporte,
											'flag_hamburguesa' => $burger,
										   	'fecha'            => $fecha);
				$datoInsert  = $this->M_Datos->insertarDatos($insertParticipante,'contact');
				$this->sendConfirmation($correo);
	          	$data['msj']   = $datoInsert['msj'];
	          	$data['error'] = $datoInsert['error'];
	          }
		} catch(Exception $ex) {
			$data['msj'] = $ex->getMessage();
		}
      	echo json_encode($data);
	}
	function sendConfirmation($correo){
		$data['error'] = EXIT_ERROR;
		$data['msj']   = null;
		try {  
			$this->load->library("email");
			$configGmail = array('protocol'  => 'smtp',
			                     'smtp_host' => 'mail.iradianty.com',
			                     'smtp_port' => 587,
			                     'smtp_user' => 'info@iradianty.com',
			                     'smtp_pass' => 'EduardoBenavides2019!',
			                     'mailtype'  => 'html',
			                     'charset'   => 'utf-8',
			                     'newline'   => "\r\n");    
			$this->email->initialize($configGmail);
			$this->email->from('info@iradianty.com');
			$this->email->to($correo);
			// $this->email->to('jose.minayac15@gmail.com');
			$this->email->subject('Invitación HPE JustRightIT.');
			$texto = '<!DOCTYPE html>
			                <html>
			                    <body>
			                        <table width="500" cellpadding="0" cellspacing="0" align="center" style="border: solid 1px #ccc;">
			                            <tr>
			                                <td style="background-color: #415564;width:100%;">
			                                    <table width="500" cellspacing="0" cellpadding="0" border="0" style="background-color: #415564;padding: 10px 20px;width: 100%;">
			                                        <tr>
														<td><a href="#"><img src="http://iradianty.com/HPE/events/microsite/JustRightIT/public/img/logo/hpe-logo.png" width="125" alt="alternative text" border="0" style="display: block;"></a></td>
			                                        </tr>
			                                    </table>
			                                </td>
			                            </tr>
			                            <tr>
			                                <td>
			                                    <table width="400" cellspacing="0" cellpadding="0" border="0" align="center" style="padding: 30px 10px">
			                                        <tr>
			                                            <td style="text-align: center;padding: 0;margin: 0;padding-bottom: 10px"><font style="font-family: arial;color: #000000;font-size: 18px;font-weight: 600">Muchas gracias.<br> Su registro ha sido realizado con éxito.</font></td>
													</tr>
			                                        <tr>
			                                            <td style="text-align: center;"><font style="font-family: arial;color: #757575;font-size: 12px;">&copy;Copyright 2019 Hewlett Packard Enterprise Development LP</font></td>
			                                        </tr>
			                                    </table>
			                                </td>
			                            </tr>
			                        </table>
			                    </body>
			                </html>';
			$this->email->message($texto);
			$this->email->send();
			$data['error'] = EXIT_SUCCESS;
		}catch (Exception $e){
			$data['msj'] = $e->getMessage();
		}
		return json_encode(array_map('utf8_encode', $data));
	}
	function ingresar() {
		$data['error'] = EXIT_ERROR;
        $data['msj']   = null;
         try {
			$correo   = $this->input->post('correo');
			$username = $this->M_Datos->getDatosCorreos();
			if(count($username) != 0) {
				foreach ($username as $key) {
					if ($key->email == $correo) {
						$session = array('email' => $key->email);
                        $this->session->set_userdata($session);
						$data['error'] = EXIT_SUCCESS;
					}
				}
			}
        }catch(Exception $e) {
           $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
	}
	function quiz(){
		$data['error'] = EXIT_ERROR;
      	$data['msj']   = null;
		try {
			$pregunta1   	   = $this->input->post('Pregunta1');
			$pregunta2  	   = $this->input->post('Pregunta2');
			$pregunta3  	   = $this->input->post('Pregunta3');
			$pregunta4 	 	   = $this->input->post('Pregunta4');
			$pregunta5  	   = $this->input->post('Pregunta5');
			$codigo      	   = $this->input->post('Codigo');
			$correo     	   = $this->session->userdata('email');
			$actualizarParticipante = array('pregunta1' 	    => $pregunta1,
											'pregunta2' 	    => $pregunta2,
											'pregunta3' 	    => $pregunta3,
											'pregunta4' 	    => $pregunta4,
											'pregunta5' 	    => $pregunta5,
											'codigo'            => $codigo);
			$datoInsert  = $this->M_Datos->actualizarDatos($correo,'contact', $actualizarParticipante);
			$this->sendCupo($correo,$codigo);
          	$data['msj']   = $datoInsert['msj'];
          	$data['error'] = $datoInsert['error'];
		} catch(Exception $ex) {
			$data['msj'] = $ex->getMessage();
		}
      	echo json_encode($data);
	}
	function sendCupo($correo,$codigo){
		$data['error'] = EXIT_ERROR;
		$data['msj']   = null;
		try {  
			$this->load->library("email");
			$configGmail = array('protocol'  => 'smtp',
			                     'smtp_host' => 'mail.iradianty.com',
			                     'smtp_port' => 587,
			                     'smtp_user' => 'info@iradianty.com',
			                     'smtp_pass' => 'EduardoBenavides2019!',
			                     'mailtype'  => 'html',
			                     'charset'   => 'utf-8',
			                     'newline'   => "\r\n");    
			$this->email->initialize($configGmail);
			$this->email->from('info@iradianty.com');
			$this->email->to($correo);
			// $this->email->to('jose.minayac15@gmail.com');
			$this->email->subject('Gracias por participar.');
			$texto = '<!DOCTYPE html>
			                <html>
			                    <body>
			                        <table width="500" cellpadding="0" cellspacing="0" align="center" style="border: solid 1px #ccc;">
			                            <tr>
			                                <td style="background-color: #415564;width:100%;">
			                                    <table width="500" cellspacing="0" cellpadding="0" border="0" style="background-color: #415564;padding: 10px 20px;width: 100%;">
			                                        <tr>
														<td><a href="#"><img src="http://iradianty.com/HPE/events/microsite/JustRightIT/public/img/logo/hpe-logo.png" width="125" alt="alternative text" border="0" style="display: block;"></a></td>
			                                        </tr>
			                                    </table>
			                                </td>
			                            </tr>
			                            <tr>
			                                <td>
			                                    <table width="400" cellspacing="0" cellpadding="0" border="0" align="center" style="padding: 30px 10px">
			                                        <tr>
			                                            <td style="text-align: center;padding: 0;margin: 0;padding-bottom: 10px"><font style="font-family: arial;color: #000000;font-size: 18px;font-weight: 600">Gracias por participar.!</font></td>
													</tr>
													<tr>
														<td><a href="#"><img src="http://iradianty.com/HPE/events/microsite/JustRightIT/public/img/fondo/regalo.png" width="250" alt="alternative text" border="0" style="display: block;margin: auto;"></a></td>
													</tr>
													<tr>
														<td style="text-align: center;padding: 0;margin: 0;padding-bottom: 10px;padding-top:10px;"><font style="font-family: arial;color: #000000;font-size: 20px;font-weight: 600">'.$codigo.'</font></td>
													</tr>
													<tr>
														<td style="text-align: center;padding: 0;margin: 0;"><font style="font-family: arial;color: #000000;font-size: 16px;font-weight: 100">El souvenir solo se entrega el día del evento</font></td>
													</tr>
													<tr>
														<td style="text-align: center;padding: 0;margin: 0;"><font style="font-family: arial;color: #000000;font-size: 16px;font-weight: 100">No es transferible, la entrega es personal.</font></td>
													</tr>
													<tr>
														<td style="text-align: center;padding: 0;margin: 0;"><font style="font-family: arial;color: #000000;font-size: 16px;font-weight: 100">Presentar un documento de indentidad</font></td>
													</tr>
													<tr>
														<td style="text-align: center;padding: 0;margin: 0;padding-bottom: 10px"><font style="font-family: arial;color: #000000;font-size: 16px;font-weight: 100">Los datos compartidos deberán ser correctos.</font></td>
													</tr>
			                                        <tr>
			                                            <td style="text-align: center;"><font style="font-family: arial;color: #757575;font-size: 12px;">&copy;Copyright 2019 Hewlett Packard Enterprise Development LP</font></td>
			                                        </tr>
			                                    </table>
			                                </td>
			                            </tr>
			                        </table>
			                    </body>
			                </html>';
			$this->email->message($texto);
			$this->email->send();
			$data['error'] = EXIT_SUCCESS;
		}catch (Exception $e){
			$data['msj'] = $e->getMessage();
		}
		return json_encode(array_map('utf8_encode', $data));
	}
	function ingresarSite(){
		$data['error'] = EXIT_ERROR;
        $data['msj']   = null;
         try {
			$password = $this->input->post('PasswordSite');
			if($password == 'arubamascercadeti'){
				$data['error'] = EXIT_SUCCESS;
			}else {
				$data['msj'] = 'Password incorrecto';
			}
        }catch(Exception $e) {
           $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
	}
}
