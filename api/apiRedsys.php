<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * API needed to implement a payment gallery in the plugin.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * NOTA SOBRE LA LICENCIA DE USO DEL SOFTWARE
 *
 * El uso de este software está sujeto a las Condiciones de uso de software que
 * se incluyen en el paquete en el documento "Aviso Legal.pdf". También puede
 * obtener una copia en la siguiente url:
 * http://www.redsys.es/wps/portal/redsys/publica/areadeserviciosweb/descargaDeDocumentacionYEjecutables
 *
 * Redsys es titular de todos los derechos de propiedad intelectual e industrial
 * del software.
 *
 * Quedan expresamente prohibidas la reproducción, la distribución y la
 * comunicación pública, incluida su modalidad de puesta a disposición con fines
 * distintos a los descritos en las Condiciones de uso.
 *
 * Redsys se reserva la posibilidad de ejercer las acciones legales que le
 * correspondan para hacer valer sus derechos frente a cualquier infracción de
 * los derechos de propiedad intelectual y/o industrial.
 *
 * Redsys Servicios de Procesamiento, S.L., CIF B85955367
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class RedsysApi.
 *
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class redsysapi{

    /******  Array de DatosEntrada ******/
    /**
     * @var $pay variable of payment data
     */
    public $pay = array();

    /******  Set parameter ******/
    /**
     * setparameter
     * @param array $key
     * @param array $value
     */
    public function setparameter($key, $value) {
        $this->pay[$key] = $value;
    }

    /******  Get parameter ******/
    /**
     * getparameter
     * @param array $key
     * @return $this->pay[$key]
     */
    public function getparameter($key) {
        return $this->pay[$key];
    }

    /******  3DES Function  ******/
    /**
     * encrypt_3des
     * @param string $message
     * @param array $key
     * @return $ciphertext
     */
    public function encrypt_3des($message, $key) {
        // Se establece un IV por defecto.
        $l = ceil(strlen($message) / 8) * 8;
        $message = $message.str_repeat("\0", $l - strlen($message));

        // Se cifra.
        return substr(openssl_encrypt($message, 'des-ede3-cbc', $key, OPENSSL_RAW_DATA, "\0\0\0\0\0\0\0\0"), 0, $l);
    }

    /******  Base64 Functions  ******/
    /**
     * Encode URL to base64
     * @param string $input
     * @return strtr(base64_encode($input), '+/', '-_')
     */
    public function base64_url_encode($input) {
        return strtr(base64_encode($input), '+/', '-_');
    }
    /**
     * Encode to base64
     * @param array $data
     * @return array $data
     */
    public function encodebase64($data) {
        $data = base64_encode($data);
        return $data;
    }
    /**
     * Decode URL on base64
     * @param string $input
     * @return base64_decode(strtr($input, '-_', '+/'));
     */
    public function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }
    /**
     * Decode on base64
     * @param array $data
     * @return array $data
     */
    public function decodebase64($data) {
        $data = base64_decode($data);
        return $data;
    }

    /******  MAC Function ******/
    /**
     * mac256
     * @param array $ent
     * @param array $key
     * @return array $res
     */
    public function mac256($ent, $key) {
        $res = hash_hmac('sha256', $ent, $key, true);// PHP 5 >= 5.1.2 .
        return $res;
    }

    /******  Obtener Número de pedido ******/
    /**
     * getorder
     * @return int $numpedido
     */
    public function getorder() {
        $numpedido = "";
        if (empty($this->pay['DS_MERCHANT_ORDER'])) {
            $numpedido = $this->pay['Ds_Merchant_Order'];
        } else {
            $numpedido = $this->pay['DS_MERCHANT_ORDER'];
        }
        return $numpedido;
    }
    /******  Convertir Array en Objeto JSON ******/
     /**
      * array to json
      * @return $json object on json
      */
    public function arraytojson() {
        $json = json_encode($this->pay); // PHP 5 >= 5.2.0 .
        return $json;
    }
     /**
      * Create Merchant Parameters
      * @return $json
      */
    public function createmerchantparameters() {
        // Se transforma el array de datos en un objeto Json.
        $json = $this->arraytojson();
        // Se codifican los datos Base64.
        return $this->encodebase64($json);
    }
     /**
      * Create Merchant Signature
      * @param array $key
      * @return $this->encodebase64($res)
      */
    public function createmerchantsignature($key) {
        // Se decodifica la clave Base64.
        $key = $this->decodebase64($key);
        // Se genera el parámetro Ds_MerchantParameters.
        $ent = $this->createmerchantparameters();
        // Se diversifica la clave con el Número de Pedido.
        $key = $this->encrypt_3des($this->getorder(), $key);
        // MAC256 del parámetro Ds_MerchantParameters.
        $res = $this->mac256($ent, $key);
        // Se codifican los datos Base64.
        return $this->encodebase64($res);
    }

    /******  Obtener Número de pedido ******/
     /**
      * getordernotif
      * @return int $numpedido
      */
    public function getordernotif() {
        $numpedido = "";
        if (empty($this->pay['Ds_Order'])) {
            $numpedido = $this->pay['DS_ORDER'];
        } else {
            $numpedido = $this->pay['Ds_Order'];
        }
        return $numpedido;
    }
     /**
      * Get order notification SOAP
      * @param array $datos
      * @return substr($datos, $pospedidoini + $tampedidoini, $pospedidofin - ($pospedidoini + $tampedidoini))
      */
    public function getordernotifsoap($datos) {
        $pospedidoini = strrpos($datos, "<Ds_Order>");
        $tampedidoini = strlen("<Ds_Order>");
        $pospedidofin = strrpos($datos, "</Ds_Order>");
        return substr($datos, $pospedidoini + $tampedidoini, $pospedidofin - ($pospedidoini + $tampedidoini));
    }
    /**
     * Get request notification SOAP
     * @param array $datos
     * @return substr($datos, $posreqini, ($posreqfin + $tamreqfin) - $posreqini)
     */
    public function getrequestnotifsoap($datos) {
        $posreqini = strrpos($datos, "<Request");
        $posreqfin = strrpos($datos, "</Request>");
        $tamreqfin = strlen("</Request>");
        return substr($datos, $posreqini, ($posreqfin + $tamreqfin) - $posreqini);
    }
    /**
     * Get response notification SOAP
     * @param array $datos
     * @return substr($datos, $posreqini, ($posreqfin + $tamreqfin) - $posreqini)
     */
    public function getresponsenotifsoap($datos) {
        $posreqini = strrpos($datos, "<Response");
        $posreqfin = strrpos($datos, "</Response>");
        $tamreqfin = strlen("</Response>");
        return substr($datos, $posreqini, ($posreqfin + $tamreqfin) - $posreqini);
    }
    /* Convertir String en Array. */
    /**
     * stringtoarray
     * @param array $datosdecod
     */
    public function stringtoarray($datosdecod) {
        $this->pay = json_decode($datosdecod, true);// PHP 5 >= 5.2.0 .
    }
    /**
     * Decode Merchant Parameters
     * @param array $datos
     * @return array $decodec
     */
    public function decodemerchantparameters($datos) {
        // Se decodifican los datos Base64.
        $decodec = $this->base64_url_decode($datos);
        // Los datos decodificados se pasan al array de datos.
        $this->stringtoarray($decodec);
        return $decodec;
    }
    /**
     * Create Merchant Signature Notification
     * @param array $key
     * @param array $datos
     * @return $this->base64_url_encode($res)
     */
    public function createmerchantsignaturenotif($key, $datos) {
        // Se decodifica la clave Base64.
        $key = $this->decodebase64($key);
        // Se decodifican los datos Base64.
        $decodec = $this->base64_url_decode($datos);
        // Los datos decodificados se pasan al array de datos.
        $this->stringtoarray($decodec);
        // Se diversifica la clave con el Número de Pedido.
        $key = $this->encrypt_3des($this->getordernotif(), $key);
        // MAC256 del parámetro Ds_Parameters que envía Redsys.
        $res = $this->mac256($datos, $key);
        // Se codifican los datos Base64.
        return $this->base64_url_encode($res);
    }
    /******  Notificaciones SOAP ENTRADA ******/
    /**
     * Create Merchant Signature Notif SOAP Request
     * @param array $key
     * @param array $datos
     * @return $this->encodebase64($res)
     */
    public function createmerchantsignaturenotifsoaprequest($key, $datos) {
        // Se decodifica la clave Base64.
        $key = $this->decodebase64($key);
        // Se obtienen los datos del Request.
        $datos = $this->getrequestnotifsoap($datos);
        // Se diversifica la clave con el Número de Pedido.
        $key = $this->encrypt_3des($this->getordernotifsoap($datos), $key);
        // MAC256 del parámetro Ds_Parameters que envía Redsys.
        $res = $this->mac256($datos, $key);
        // Se codifican los datos Base64.
        return $this->encodebase64($res);
    }
    /******  Notificaciones SOAP SALIDA ******/
    /**
     * Create Merchant Signature Notif SOAP Response
     * @param array $key
     * @param array $datos
     * @param int $numpedido
     * @return $this->encodebase64($res)
     */
    public function createmerchantsignaturenotifsoapresponse($key, $datos, $numpedido) {
        // Se decodifica la clave Base64.
        $key = $this->decodebase64($key);
        // Se obtienen los datos del Request.
        $datos = $this->getresponsenotifsoap($datos);
        // Se diversifica la clave con el Número de Pedido.
        $key = $this->encrypt_3des($numpedido, $key);
        // MAC256 del parámetro Ds_Parameters que envía Redsys.
        $res = $this->mac256($datos, $key);
        // Se codifican los datos Base64.
        return $this->encodebase64($res);
    }
}
