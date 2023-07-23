<div class="row d-flex justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="card shadow-0 border" style="background-color: #f0f2f5;">
      <div class="card-body p-4">
        <div id="error"></div>
        <div class="d-flex justify-content-between my-3">
          <div class="form-group">
            <label for="sortSelect" class="mb-2">Sort by:</label>
            <select class="form-control" id="sortSelect">
              <option value="date">Update Date</option>
              <option value="id">ID</option>
            </select>
          </div>
          <div class="form-group">
            <label for="directionSelect" class="mb-2">Sort direction:</label>
            <select class="form-control" id="directionSelect">
              <option value="asc">ASC</option>
              <option value="desc">DESC</option>
            </select>
          </div>
        </div>
        <div id="commentData">
          <div id="commentList">
            <?php if (!empty($comments) && is_array($comments)): ?>
              <?php foreach ($comments as $key => $comment): ?>
                <div class="card mb-4 shadow-sm">
                  <div class="card-body">
                    <div class="d-flex justify-content-between">
                      <p>
                        <?=$comment['text'] ?>
                      </p>
                      <button type="button" class="close delete-comment" aria-label="Close" data-id="<?=$comment[ 'id']?>">
                        <span aria-hidden="true">×</span>
                      </button>
                    </div>
                    <div class="d-flex justify-content-between">
                      <div class="d-flex flex-row align-items-center">
                        <p class="small mb-0 ms-2">
                          <?=$comment['name'] ?>
                        </p>
                      </div>
                      <div class="d-flex flex-row align-items-center">
                        <p class="small text-muted mb-0">
                          <?=$comment['date'] ?>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
            <?php endforeach ?>
          <?php else: ?>
            <h4 class="text-center">No comments yet</h4>
          <?php endif ?>
          </div>
          <nav id="pagination">
          <?php if (!empty($comments) && is_array($comments)): ?>
              <ul class="pagination justify-content-center">
                <?php foreach ($pages as $pageNum): ?>
                  <li class="page-item <?= $pageNum == 1 ? 'active' : '' ?>">
                    <button class="page-link" data-page="<?= $pageNum ?>">
                      <?= $pageNum ?>
                    </button>
                  </li>
                <?php endforeach ?>
              </ul>
            <?php endif; ?>
          </nav>
          <form id="commentForm" class="mb-4">
            <div class="mb-3">
              <input class="form-control" minlength="3" type="email" id="emailArea" placeholder="Email" required>
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
</div>

<script type="text/javascript">
  $(document).ready(function() {

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
          let error = 'Something is wrong: ' + textStatus + ', ' + errorThrown;
          $('#error').html('<div class="alert alert-danger" role="alert">' + error + '</div>');
        },
        success: function(data) {
          updateList();
        }
      });
    }

    function updateList() {
      $('#error').html('');
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
          let error = 'Something is wrong: ' + textStatus + ', ' + errorThrown;
          $('#error').html('<div class="alert alert-danger" role="alert">' + error + '</div>');
        },
        success: function(data) {
          let elements = '';
          if (data.comments.length == 0) {
            if (currentPage == 1) {
              $('#commentList').html('<h4 class="text-center">No comments yet</h4>');
              $('#pagination').html('');
              return;
            }
            while (currentPage != 1) {
              currentPage--;
              updateList();
            }
            return;
          } else {
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

            let pagination = '<ul class="pagination justify-content-center">';
            data.pages.forEach(pageNum => {
                pagination += '<li class="page-item ' + (pageNum == currentPage ? 'active' : '') + '">' +
                    '<button class="page-link" data-page="' + pageNum + '">' +
                    pageNum +
                    '</button>' +
                    '</li>';
            });
            pagination += '</ul>';
            $('#pagination').html(pagination);
           }
          }
        });
    }

    function addNewComment() {
      let name = $('#emailArea').val();
      let text = $('#commentArea').val();
      $.ajax({
        url: '/comments',
        type: 'POST',
        data: {
          name: name,
          text: text,
        },
        error: function() {
          let error = 'Something is wrong: ' + textStatus + ', ' + errorThrown;
          $('#error').html('<div class="alert alert-danger" role="alert">' + error + '</div>');
        },
        success: function(data) {
          if (data.errors != '') {
            $('#error').html('<div class="alert alert-danger" role="alert">' + data.errors.name + '</div>');
            return;
          }
          currentPage = 1;
          updateList();
        }
      });

      $('#emailArea').val('');
      $('#commentArea').val('');
    };
  });
</script>