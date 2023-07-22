<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentsModel extends Model
{
	protected $table = 'comments';

	protected $allowedFields = ['name', 'text', 'date'];

	public function getComments($page = 1, $sort = 'date', $direction = 'asc')
	{
		$this->db = db_connect();
		$limit = 3;
		$offset = ($page - 1) * $limit;

		$builder = $this->db->table('comments');
		$builder->select('id, name, text, date');
		$builder->orderBy($sort, $direction);
		$query = $builder->get($limit, $offset);

		$data = [];
		foreach ($query->getResult('array') as $key => $row) {
			$data[$key]['id'] = $row['id'];
			$data[$key]['name'] = $row['name'];
			$data[$key]['text'] = $row['text'];
			$data[$key]['date'] = $row['date'];
		}

		$totalComments = $this->db->table('comments')->countAllResults();

		$totalPages = ceil($totalComments / $limit);

		$startPage = max(1, $page - 2);
		$endPage = min($totalPages, $page + 2);
		$pages = range($startPage, $endPage);

		return [
			'comments' => $data,
			'pages' => $pages
		];
	}


	public function deleteComment($id)
	{
		$this->db = db_connect();
		$builder = $this->db->table('comments');
		$builder->delete(['id' => $id]);
		$error = $this->db->error();
		if ($error['code'] !== 0) {
			return $error['message'];
		}
		return null;
	}
}