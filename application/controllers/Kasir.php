<?php
defined('BASEPATH') or exit('No direct script access allowed');
// Don't forget include/define RestController path

use chriskacerguis\RestServer\RestController;

class Kasir extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('kasir_model', 'ksr');
        $this->methods['index_get']['limit'] = 2;
    }

    public function index_get()
    {
        $id = $this->get('id', true);
        if ($id === null) {
          $p = $this->get('page', true);
          $p = (empty($p) ? 1 : $p);
          $total_data = $this->ksr->count();
          $total_page = ceil($total_data / 5);
          $start = ($p - 1) * 5;
          $list = $this->ksr->get(null, 5, $start);
          if ($list) {
            $data = [
              'status' => true,
              'page' => $p,
              'total_data' => $total_data,
              'total_page' => $total_page,
              'data' => $list
            ];
          } else {
            $data = [
              'status' => false,
              'msg' => 'Data tidak ditemukan'
            ];
          }
          $this->response($data, RestController::HTTP_OK);
        } else {
          $data = $this->ksr->get($id);
          if ($data) {
            $this->response(['status' => true, 'data' => $data], RestController::HTTP_OK);
          } else {
            $this->response(['status' => false, 'msg' => $id . ' tidak ditemukan'], RestController::HTTP_NOT_FOUND);
          }
        }
    }
  
    public function index_post()
    {
      $data = [
        'id_kasir' => $this->post('id', true),
        'nama' => $this->post('nama', true),
        'alamat' => $this->post('alamat', true),
        'telepon' => $this->post('telepon', true),
        'status' => $this->post('status', true)
      ];
      $simpan = $this->ksr->add($data);
      if ($simpan['status']) {
        $this->response(['status' => true, 'msg' => $simpan['data'] . ' Data telah ditambahkan'], RestController::HTTP_CREATED);
      } else {
        $this->response(['status' => false, 'msg' => $simpan['msg']], RestController::HTTP_INTERNAL_ERROR);
      }
    }
  
    public function index_put()
    {
      $data = [
        'id_kasir' => $this->put('id', true),
        'nama' => $this->put('nama', true),
        'alamat' => $this->put('alamat', true),
        'telepon' => $this->put('telepon', true),
        'status' => $this->put('status', true)
      ];
      $id = $this->put('id', true);
      if ($id === null) {
        $this->response(['status' => false, 'msg' => 'Masukkan ID Kasir yang akan dirubah'], RestController::HTTP_BAD_REQUEST);
      }
      $simpan = $this->ksr->update($id, $data);
      if ($simpan['status']) {
        $status = (int)$simpan['data'];
        if ($status > 0)
          $this->response(['status' => true, 'msg' => $simpan['data'] . ' Data telah dirubah'], RestController::HTTP_OK);
        else
          $this->response(['status' => false, 'msg' => 'Tidak ada data yang dirubah'], RestController::HTTP_BAD_REQUEST);
      } else {
        $this->response(['status' => false, 'msg' => $simpan['msg']], RestController::HTTP_INTERNAL_ERROR);
      }
    }
  
    public function index_delete()
    {
      $id = $this->delete('id', true);
      if ($id === null) {
        $this->response(['status' => false, 'msg' => 'Masukkan Id kasir yang akan dihapus'], RestController::HTTP_BAD_REQUEST);
      }
      $delete = $this->ksr->delete($id);
      if ($delete['status']) {
        $status = (int)$delete['data'];
        if ($status > 0)
          $this->response(['status' => true, 'msg' => $id . ' data telah dihapus'], RestController::HTTP_OK);
        else
          $this->response(['status' => false, 'msg' => 'Tidak ada data yang dihapus'], RestController::HTTP_BAD_REQUEST);
      } else {
        $this->response(['status' => false, 'msg' => $delete['msg']], RestController::HTTP_INTERNAL_ERROR);
      }
    }
  }

