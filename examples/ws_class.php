<?php

// Importamos la clase Whatsapp del espacio de nombres Adrii\Whatsapp
use Adrii\Whatsapp\Whatsapp;

// Definimos una clase llamada whatsappController
class whatsappController
{
    // Propiedades protegidas de la clase
    protected $graph_version = "v16.0"; // Versión del API de Graph que se usará
    protected $phone_number_id = "";    // Identificador del número de teléfono asociado
    protected $access_token = "";       // Token de acceso para la API
    protected $ws;                      // Variable para instanciar la clase Whatsapp

    // Constructor de la clase
    public function __construct()
    {
        // Inicializa la clase Whatsapp con los parámetros necesarios
        $this->ws = new Whatsapp($this->phone_number_id, $this->access_token, $this->graph_version);
    }

    // Método para manejar webhooks
    public function webhook()
    {
        // Obtiene el método HTTP usado en la solicitud actual
        $method = $GLOBALS['_SERVER']['REQUEST_METHOD'];
        switch ($method) {
            case "GET": // Si el método es GET
                $this->get();
                break;
            case "POST": // Si el método es POST
                $this->save();
                break;
            case "PUT": // Si el método es PUT
                // No realiza ninguna acción en caso de PUT
                break;
        }
    }

    // Método para manejar solicitudes GET
    public function get()
    {
        // Verifica si hay datos en la variable global $_GET
        if (!empty($_GET)) {
            // Conecta el webhook y pasa los parámetros GET
            $this->ws->webhook()->connect($_GET);
        }

        // Envía una respuesta HTTP con código 404 si no se cumplen condiciones anteriores
        $this->response(array('code' => '404'), 404);
    }

    // Método para iniciar un proceso o acción
    public function start()
    {
        // Almacena los datos recibidos por POST
        $data = $_POST;

        // Crea un componente de cabecera para enviar en un mensaje
        $component_header = array(
            "type" => "header",
            "parameters" => array(
                array(
                    "type" => "text",
                    "text" => $data['name']
                )
            )
        );

        // Envía el componente como parte de un mensaje
        $this->ws->send_message()->addComponent($component_header);
        // Envía un mensaje con una plantilla específica
        $this->ws->send_message()->template("template_id", $data['recipient_id'], "ES");

        // Envía una respuesta HTTP con código 200
        $this->response(array("code" => 200), 200);
    }

    // Método para guardar datos recibidos via POST
    public function save()
    {
        // Almacena los datos recibidos por POST
        $data = $_POST;

        // Verifica si el array $data está vacío
        if (empty($data)) {
            // Responde con código 400 si no hay datos
            $this->response(array('code' => '400'), 400);
        }

        // Verifica si existen ciertas claves en el array $data
        if (
            isset($data['entry']) &&
            isset($data['entry'][0]) &&
            isset($data['entry'][0]['changes']) &&
            isset($data['entry'][0]['changes'][0]) &&
            isset($data['entry'][0]['changes'][0]['value']) &&
            isset($data['entry'][0]['changes'][0]['value']['messages']) &&
            isset($data['entry'][0]['changes'][0]['value']['messages'][0])
        ) {

            // Extrae información específica de los cambios recibidos
            $msg = $data['entry'][0]['changes'][0]['value']['messages'][0];
            $user_id = $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
            $msg_type = $msg['type'];

            // Responde con los datos del mensaje y usuario
            $this->response(array(
                'code' => '200',
                $data => array(
                    "msg" => $msg,
                    "user_id" => $user_id,
                    "msg_type" => $msg_type
                )
            ), 200);
        }

        // Responde con código 400 si no se cumplen las condiciones para extraer datos
        $this->response(array('code' => '400'), 400);
    }

    // Método privado para enviar respuestas HTTP
    private function response($data, $http_code = 200)
    {
        // Establece el tipo de contenido como JSON y envía los códigos de estado HTTP
        header('Content-Type: application/json');
        header('HTTP/1.1: ' . $http_code);
        header('Status: ' . $http_code);
        exit(json_encode($data)); // Termina la ejecución y devuelve los datos en formato JSON
    }
}
