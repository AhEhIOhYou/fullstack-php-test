<?php

namespace App\Controllers;
use App\Models\CommentsModel;

use CodeIgniter\Exceptions\PageNotFoundException;

class Comments extends BaseController
{
	public function create()
	{
		$rules = [
			'name' => 'required|valid_email',
			'text' => 'required|min_length[1]|max_length[2000]'
		];

		if (!$this->validate($rules)) {
			$data = [
				'success' => false,
				'errors' => $this->validator->getErrors()
			];
		} else {
			$post = $this->request->getPost(['name', 'text']);

			$model = model(CommentsModel::class);

			$save = $model->save([
				'name' => $post['name'],
				'text'  => $post['text'],
				'date' => date('Y-m-d H:i:s'),
			]);

			$data = [
				'success' => true,
				'data' => [
					'name' => $post['name'],
					'text' => $post['text'],
				],
			];
		}

		return $this->response->setJSON($data);
	}


	public function getComments()
	{
		$page = $this->request->getVar('page');
		$sort = $this->request->getVar('sort');
		$direction = $this->request->getVar('direction');

		$model = model(CommentsModel::class);

		$data = $model->getComments($page, $sort, $direction);

		return $this->response->setJSON($data);
	}



	public function deleteComment()
	{
		$id = $this->request->getRawInput('id');

		$model = model(CommentsModel::class);

		$data = [
			'error' => $model->deleteComment($id),
		];

		return $data;
	}
}
