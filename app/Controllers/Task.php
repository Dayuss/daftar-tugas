<?php

namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Task extends ResourceController
{
    /*model*/
    protected $M_Task;
    /*db*/
    protected $db;
	use ResponseTrait;
    protected $format = 'json';

    public function __construct()
    {
        $this->task = new \App\Models\TaskModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = $this->task->findAll();
        return $this->respond($data, 200);
    }

	// get single show
    public function show($id = null)
    {
        $data = $this->task->find($id);
        if($data){
            return $this->respond($data);
        }else{
            return $this->failNotFound('No Data Found with id '.$id);
        }
    }

	// create a data
    public function create()
    {
		$judul = $this->request->getPost('judul');
		$status = $this->request->getPost('status');
        $data = [
			'judul' => $judul,
            'status' => $status
        ];

        $this->task->insert($data);
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Data Saved'
            ]
        ];
         
        return $this->respondCreated($response, 201);
    }

	// update a data
	public function update($id = null)
    {
        $data = $this->request->getRawInput();
        $data['id'] = $id;

        if(!$this->task->find($id))
        {
            return $this->fail('id tidak ditemukan');
        }

		$this->task->update($id, $data);
        $response = [
            'status'   => 200,
            'error'    => null,
            'messages' => [
                'success' => 'Data Updated'
            ]
        ];
        return $this->respond($response);
    }

	// delete a data
    public function delete($id = null)
    {
        if(!$this->task->find($id))
        {
            return $this->fail('id tidak ditemukan');
        }

        if($this->task->delete($id)){
            return $this->respondDeleted(['id'=>$id.' Deleted']);
        }
    }
}