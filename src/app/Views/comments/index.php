<div class="row d-flex justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="card shadow-0 border" style="background-color: #f0f2f5;">
      <div class="card-body p-4">
        <div id="error"></div>
        <?php if (!empty($comments) && is_array($comments)): ?>
          <div class="d-flex justify-content-between my-3">
            <div class="form-group">
                <label for="sortSelect" class="mb-2">Сортировка по:</label>
                <select class="form-control" id="sortSelect">
                    <option value="date">Дате добавления</option>
                    <option value="id">ID</option>
                </select>
            </div>
            <div class="form-group">
                <label for="directionSelect" class="mb-2">Направление сортировки:</label>
                <select class="form-control" id="directionSelect">
                    <option value="asc">По возрастанию</option>
                    <option value="desc">По убыванию</option>
                </select>
            </div>
          </div>
          <div id="commentList">
          <?php foreach ($comments as $key => $comment): ?>
            <div class="card mb-4 shadow-sm">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <p><?=$comment['text'] ?></p>
                      <button type="button" class="close delete-comment" aria-label="Close" data-id=<?=$comment['id']?>>
                          <span aria-hidden="true">×</span>
                      </button>
                  </div>
                  <div class="d-flex justify-content-between">
                      <div class="d-flex flex-row align-items-center">
                          <p class="small mb-0 ms-2"><?=$comment['name'] ?></p>
                      </div>
                      <div class="d-flex flex-row align-items-center">
                          <p class="small text-muted mb-0"><?=$comment['date'] ?></p>
                      </div>
                  </div>
              </div>
            </div>
          <?php endforeach ?>
          </div>
          <nav>
            <ul id="pagination" class="pagination justify-content-center">
                <?php foreach ($pages as $pageNum): ?>
                    <li class="page-item <?= $pageNum == 1 ? 'active' : '' ?>">
                        <button class="page-link" data-page="<?= $pageNum ?>">
                            <?= $pageNum ?>
                        </button>
                    </li>
                <?php endforeach ?>
            </ul>
          </nav>
        <?php else: ?>
          <h4 class="text-center">No comments yet</h4>
        <?php endif ?>
        <form id="commentForm" class="mb-4">
          <div class="mb-3">
            <input class="form-control" minlength="3" type="email" id="emailArea" placeholder="Email" required></input>
          </div>
          <div class="mb-3">
            <textarea class="form-control" minlength="2" id="commentArea" rows="3" placeholder="Add a comment..." required></textarea>
          </div>
          <button id="commentSend" type="submit" class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {

    let error = '';
    let currentPage = 1;
    let currentSort = 'date';
    let currentDirection = 'asc';

    $('#commentForm').on("submit", function(e) {
      e.preventDefault();
      addNewComment();
    });

    $('#pagination').on('click', 'button', function(e) {
      e.preventDefault();
      currentPage = $(this).data('page');
      updateList();
    });

    $('#commentList').on('click', '.delete-comment', function(e) {
      let deleteId = $(this).data('id');
      deleteComment(deleteId);
    });

    function showError(data) {
      $('#error').html(data);
    }

    $('#sortSelect').on('change', function() {
      currentSort = $(this).val();
      updateList();
    });

    $('#directionSelect').on('change', function() {
      currentDirection = $(this).val();
      updateList();
    });

    function deleteComment(deleteId) {
      $.ajax({
        url: '/comments',
        type: 'DELETE',
        data: {
            id: deleteId,
        },
        error: function() {
          error = ""
        },
        success: function(data) {
          console.log(data);
          updateList();
        }
      });

    }

    function updateList() {
      error = '';
      $.ajax({
        url: '/comments',
        type: 'GET',
        data: {
            page: currentPage,
            sort: currentSort,
            direction: currentDirection
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Something is wrong: ' + textStatus + ', ' + errorThrown);
        },
        success: function(data) {
          let elements = '';
          data.comments.forEach(element => {
            elements +=
              '<div class="card mb-4 shadow-sm">' +
                  '<div class="card-body">' +
                      '<div class="d-flex justify-content-between">' +
                          '<p>' + element.text + '</p>' +
                          '<button type="button" class="close delete-comment" aria-label="Close" data-id=' + element.id + '>' +
                              '<span aria-hidden="true">×</span>' +
                          '</button>' +
                      '</div>' +
                      '<div class="d-flex justify-content-between">' +
                          '<div class="d-flex flex-row align-items-center">' +
                              '<p class="small mb-0 ms-2">' + element.name + '</p>' +
                          '</div>' +
                          '<div class="d-flex flex-row align-items-center">' +
                              '<p class="small text-muted mb-0">' + element.date + '</p>' +
                          '</div>' +
                      '</div>' +
                  '</div>' +
              '</div>';
            });
            $('#commentList').html(elements);

            let pagination = '';
            data.pages.forEach(pageNum => {
                pagination += '<li class="page-item ' + (pageNum == currentPage ? 'active' : '') + '">' +
                    '<button class="page-link" data-page="' + pageNum + '">' +
                    pageNum +
                    '</button>' +
                    '</li>';
            });
            $('#pagination').html(pagination);
           }
        });
    }

    function addNewComment() {
      let name = $('#emailArea').val();
      let text = $('#commentArea').val();

      if (error != '') {
        $('#error').html('<div class="alert alert-danger" role="alert">' + error + '</div>');
        return;
      }

      $.ajax({
        url: '/comments',
        type: 'POST',
        data: {
          name: name,
          text: text,
        },
        error: function() {
          alert('Something is wrong');
        },
        success: function(data) {
          currentPage = 1;
          updateList();
        }
      });

      $('#emailArea').val('');
      $('#commentArea').val('');
    };
  });
</script>