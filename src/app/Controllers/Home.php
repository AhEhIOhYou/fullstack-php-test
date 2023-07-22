<?php

namespace App\Controllers;
use App\Models\CommentsModel;

use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController
{
	public function index()
	{
		$model = model(CommentsModel::class);

		$commentsData = $model->getComments();
		$comments = $commentsData['comments'];
		$pages = $commentsData['pages'];

		$data = [
			'comments' => $comments,
			'pages' => $pages,
			'title' => 'Comments',
			'error' => '',
		];

		return view('templates/header', $data)
			. view('comments/index', $data)
			. view('templates/footer');
	}
}
