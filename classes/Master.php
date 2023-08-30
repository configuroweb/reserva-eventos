<?php
require_once('../config.php');
class Master extends DBConnection
{
	private $settings;
	public function __construct()
	{
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct()
	{
		parent::__destruct();
	}
	function capture_err()
	{
		if (!$this->conn->error)
			return false;
		else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_message()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if (!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if (empty($id)) {
			$sql = "INSERT INTO `message_list` set {$data} ";
		} else {
			$sql = "UPDATE `message_list` set {$data} where id = '{$id}' ";
		}

		$save = $this->conn->query($sql);
		if ($save) {
			$rid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if (empty($id))
				$resp['msg'] = "Tu mensaje ha sido enviado correctamente";
			else
				$resp['msg'] = "Tu mensaje ha sido actualizado correctamente";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Ocurrió un error";
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		if ($resp['status'] == 'success' && !empty($id))
			$this->settings->set_flashdata('success', $resp['msg']);
		if ($resp['status'] == 'success' && empty($id))
			$this->settings->set_flashdata('pop_msg', $resp['msg']);
		return json_encode($resp);
	}
	function delete_message()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `message_list` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "El mensaje ha sido eliminado, correctamente");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_event()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if (!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if (empty($id)) {
			$sql = "INSERT INTO `event_list` set {$data} ";
		} else {
			$sql = "UPDATE `event_list` set {$data} where id = '{$id}' ";
		}
		$check = $this->conn->query("SELECT * FROM `event_list` where `name` = '{$name}' and delete_flag = 0 " . ($id > 0 ? " and id != '{$id}'" : ""));
		if ($check->num_rows > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = " Event Name already exists.";
		} else {
			$save = $this->conn->query($sql);
			if ($save) {
				$rid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['status'] = 'success';
				if (empty($id))
					$resp['msg'] = " El evento ha sido agregado exitosamente";
				else
					$resp['msg'] = " El evento ha sido actualizado exitosamente";
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "Ocurrió un error";
				$resp['err'] = $this->conn->error . "[{$sql}]";
			}
		}
		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function delete_event()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `event_list` set delete_flag = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Evento ha sido eliminado correctamente");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_package()
	{
		$_POST['description'] = htmlentities($_POST['description']);
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if (!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if (empty($id)) {
			$sql = "INSERT INTO `package_list` set {$data} ";
		} else {
			$sql = "UPDATE `package_list` set {$data} where id = '{$id}' ";
		}
		$check = $this->conn->query("SELECT * FROM `package_list` where `name` ='{$name}' and delete_flag = 0 " . ($id > 0 ? " and id != '{$id}' " : ""))->num_rows;
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = " El paquete ya existe";
		} else {
			$save = $this->conn->query($sql);
			if ($save) {
				$rid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['status'] = 'success';
				if (empty($id))
					$resp['msg'] = " El paquete ha sido agregado correctamente";
				else
					$resp['msg'] = " El paquete ha sido actualizado correctamente";
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "Ocurrió un error";
				$resp['err'] = $this->conn->error . "[{$sql}]";
			}
			if ($resp['status'] == 'success')
				$this->settings->set_flashdata('success', $resp['msg']);
		}
		return json_encode($resp);
	}
	function delete_package()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `package_list` set delete_flag = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " El paquete ha sido eliminado correctamente");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_book()
	{
		if (empty($_POST['id'])) {
			$prefix = date("Ym-");
			$code = sprintf("%'.05d", 1);
			while (true) {
				$check = $this->conn->query("SELECT * FROM `booking_list` where `code` = '{$prefix}{$code}' ")->num_rows;
				if ($check > 0) {
					$code = sprintf("%'.05d", ceil($code) + 1);
				} else {
					break;
				}
			}
			$_POST['code'] = $prefix . $code;
		}
		extract($_POST);
		$data = "";
		$referer = $_SERVER['HTTP_REFERER'];
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!is_numeric($v) && !is_null($v))
					$v = $this->conn->real_escape_string($v);
				if (!empty($data)) $data .= ",";
				if (!is_null($v))
					$data .= " `{$k}`='{$v}' ";
				else
					$data .= " `{$k}`= NULL ";
			}
		}
		if (empty($id)) {
			$sql = "INSERT INTO `booking_list` set {$data} ";
		} else {
			$sql = "UPDATE `booking_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if ($save) {
			$rid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if (empty($id)) {
				if (strpos($referer, 'admin/'))
					$resp['msg'] = " La información de la reserva ha sido agregada";
				else
					$resp['msg'] = " Tu solicitud ha sido enviada exitosamente. La gerencia se comunicará con usted tan pronto como vea su solicitud de reserva. Aquí está su código de referencia de reserva: <b>{$code}</b>";
			} else
				$resp['msg'] = " Los detalles de la reserva se han actualizado correctamente.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Ocurrió un error";
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		if ($resp['status'] == 'success') {
			if (strpos($referer, 'admin/'))
				$this->settings->set_flashdata('success', $resp['msg']);
			else
				$this->settings->set_flashdata('block_success', $resp['msg']);
		}
		return json_encode($resp);
	}
	function delete_book()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `booking_list` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " La reserva ha sido eliminada correctamente");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_reservation()
	{
		$_POST['book'] = $_POST['date'] . " " . $_POST['time'];
		extract($_POST);
		$capacity = $this->conn->query("SELECT `" . ($seat_type == 1 ? "first_class_capacity" : "economy_capacity") . "` FROM event_list where id in (SELECT event_id FROM `booking_list` where id ='{$book_id}') ")->fetch_array()[0];
		$reserve = $this->conn->query("SELECT * FROM `reservation_list` where book_id = '{$book_id}' and book='{$book}' and seat_type='$seat_type'")->num_rows;
		$slot = $capacity - $reserve;
		if (count($firstname) > $slot) {
			$resp['status'] = "failed";
			$resp['msg'] = "This book has only [{$slot}] left for the selected seat type/group";
			return json_encode($resp);
		}
		$data = "";
		$sn = [];
		$prefix = $seat_type == 1 ? "FC-" : "E-";
		$seat = sprintf("%'.03d", 1);
		foreach ($firstname as $k => $v) {
			while (true) {
				$check = $this->conn->query("SELECT * FROM `reservation_list` where book_id = '{$book_id}' and book='{$book}' and seat_num = '{$prefix}{$seat}' and seat_type='$seat_type'")->num_rows;
				if ($check > 0) {
					$seat = sprintf("%'.03d", ceil($seat) + 1);
				} else {
					break;
				}
			}
			$seat_num = $prefix . $seat;
			$seat = sprintf("%'.03d", ceil($seat) + 1);
			$sn[] = $seat_num;
			if (!empty($data)) $data .= ", ";
			$data .= "('{$seat_num}','{$book_id}','{$book}','{$v}','{$middlename[$k]}','{$lastname[$k]}','{$seat_type}','{$fare_amount}')";
		}
		if (!empty($data)) {
			$sql = "INSERT INTO `reservation_list` (`seat_num`,`book_id`,`book`,`firstname`,`middlename`,`lastname`,`seat_type`,`fare_amount`) VALUES {$data}";
			$save_all = $this->conn->query($sql);
			if ($save_all) {
				$resp['status'] = 'success';
				$resp['msg'] = "Reserva enviada correctamente";
				$get_ids = $this->conn->query("SELECT id from `reservation_list` where `book_id` = '{$book_id}' and `book` = '{$book}' and seat_type='{$seat_type}' and seat_num in ('" . (implode("','", $sn)) . "') ");
				$res = $get_ids->fetch_all(MYSQLI_ASSOC);
				$ids = array_column($res, 'id');
				$ids = implode(",", $ids);
				$resp['ids'] = $ids;
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "Ocurrió un error guardando los datos. Error: " . $this->conn->error;
				$resp['sql'] = $sql;
			}
		} else {
			$resp['status'] = "failed";
			$resp['msg'] = "Sin datos para guardar";
		}


		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function delete_reservation()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `reservation_list` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "La información de la reserva ha sido eliminada correctamente");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function update_reservation_status()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `reservation_list` set `status` = '{$status}' where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "reservation Request status has successfully updated.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_reservation':
		echo $Master->save_reservation();
		break;
	case 'delete_reservation':
		echo $Master->delete_reservation();
		break;
	case 'update_reservation_status':
		echo $Master->update_reservation_status();
		break;
	case 'save_message':
		echo $Master->save_message();
		break;
	case 'delete_message':
		echo $Master->delete_message();
		break;
	case 'save_event':
		echo $Master->save_event();
		break;
	case 'delete_event':
		echo $Master->delete_event();
		break;
	case 'save_package':
		echo $Master->save_package();
		break;
	case 'delete_package':
		echo $Master->delete_package();
		break;
	case 'save_book':
		echo $Master->save_book();
		break;
	case 'delete_book':
		echo $Master->delete_book();
		break;
	default:
		// echo $sysset->index();
		break;
}
