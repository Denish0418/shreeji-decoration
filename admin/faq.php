<?php

session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    redirect("index.php");
}

/* ADD FAQ */
if (isset($_POST['add_faq'])) {

    $question = mysqli_real_escape_string($conn, trim($_POST['question']));
    $answer   = mysqli_real_escape_string($conn, trim($_POST['answer']));
    $order    = (int) ($_POST['display_order'] ?? 0);

    mysqli_query(
        $conn,
        "INSERT INTO faq (question, answer, display_order, status)
         VALUES ('$question', '$answer', '$order', 1)"
    );

    redirect("faq.php?success=1");
}

/* UPDATE FAQ */
if (isset($_POST['edit_faq'])) {

    $id       = (int) $_POST['faq_id'];
    $question = mysqli_real_escape_string($conn, trim($_POST['question']));
    $answer   = mysqli_real_escape_string($conn, trim($_POST['answer']));
    $order    = (int) ($_POST['display_order'] ?? 0);

    mysqli_query(
        $conn,
        "UPDATE faq
         SET question='$question', answer='$answer', display_order='$order'
         WHERE id='$id'"
    );

    redirect("faq.php?updated=1");
}

/* TOGGLE STATUS */
if (isset($_GET['toggle'])) {

    $id = (int) $_GET['toggle'];

    mysqli_query(
        $conn,
        "UPDATE faq
         SET status = IF(status = 1, 0, 1)
         WHERE id='$id'"
    );

    redirect("faq.php");
}

/* DELETE FAQ */
if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    mysqli_query($conn, "DELETE FROM faq WHERE id='$id'");

    redirect("faq.php?deleted=1");
}

$faqs = mysqli_query($conn, "SELECT * FROM faq ORDER BY display_order ASC, id DESC");
?>
<!DOCTYPE html>
<html>

<head>

    <title>Manage FAQs</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: #f8f5f2;
            font-family: Poppins, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 270px;
            height: 100vh;
            background: #2d140a;
            padding: 30px;
            color: #fff;
            overflow-y: auto;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 40px;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fff;
            padding: 14px 18px;
            border-radius: 14px;
        }

        .sidebar ul li a:hover {
            background: #7A2E0D;
        }

        .sidebar ul li a.active {
            background: #7A2E0D;
        }

        .main-content {
            margin-left: 270px;
            padding: 40px;
        }

        .card-box {
            background: #fff;
            padding: 30px;
            border-radius: 20px;
        }

        .btn-save {
            background: #2d140a;
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
        }

        .btn-save:hover {
            background: #7A2E0D;
            color: #fff;
        }

        .table td {
            vertical-align: middle;
        }

        .faq-question {
            max-width: 340px;
        }

        .faq-answer-preview {
            max-width: 380px;
            color: #777;
            font-size: 13px;
        }

        .status-pill {
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: #e6f7ec;
            color: #1a7f3c;
        }

        .status-inactive {
            background: #f4f4f4;
            color: #888;
        }
    </style>

</head>

<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Manage FAQs</h2>
            <button class="btn-save" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                <i class="bi bi-plus-lg me-1"></i> Add FAQ
            </button>
        </div>

        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success">FAQ added successfully.</div>
        <?php } ?>
        <?php if (isset($_GET['updated'])) { ?>
            <div class="alert alert-success">FAQ updated successfully.</div>
        <?php } ?>
        <?php if (isset($_GET['deleted'])) { ?>
            <div class="alert alert-success">FAQ deleted.</div>
        <?php } ?>

        <div class="card-box">

            <table class="table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (mysqli_num_rows($faqs) == 0) { ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No FAQs added yet. Click "Add FAQ" to create your first one.
                            </td>
                        </tr>
                    <?php } ?>

                    <?php while ($faq = mysqli_fetch_assoc($faqs)) { ?>
                        <tr>
                            <td><?= (int) $faq['id']; ?></td>
                            <td class="faq-question"><?= htmlspecialchars($faq['question']); ?></td>
                            <td class="faq-answer-preview">
                                <?php
                                    $answerPreview = $faq['answer'];
                                    if (strlen($answerPreview) > 90) {
                                        $answerPreview = substr($answerPreview, 0, 90) . '...';
                                    }
                                ?>
                                <?= htmlspecialchars($answerPreview); ?>
                            </td>
                            <td><?= (int) $faq['display_order']; ?></td>
                            <td>
                                <a href="faq.php?toggle=<?= (int) $faq['id']; ?>" class="text-decoration-none">
                                    <span class="status-pill <?= $faq['status'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                                        <?= $faq['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </a>
                            </td>
                            <td>
                                <button
                                    class="btn btn-outline-secondary btn-sm me-1 edit-faq-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editFaqModal"
                                    data-id="<?= (int) $faq['id']; ?>"
                                    data-question="<?= htmlspecialchars($faq['question'], ENT_QUOTES); ?>"
                                    data-answer="<?= htmlspecialchars($faq['answer'], ENT_QUOTES); ?>"
                                    data-order="<?= (int) $faq['display_order']; ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <a href="faq.php?delete=<?= (int) $faq['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this FAQ?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>

        </div>

    </div>

    <!-- ADD MODAL -->
    <div class="modal fade" id="addFaqModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question</label>
                        <input type="text" name="question" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer</label>
                        <textarea name="answer" rows="5" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_faq" class="btn-save">Save FAQ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editFaqModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="faq_id" id="edit_faq_id">
                    <div class="mb-3">
                        <label class="form-label">Question</label>
                        <input type="text" name="question" id="edit_question" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer</label>
                        <textarea name="answer" id="edit_answer" rows="5" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" id="edit_order" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_faq" class="btn-save">Update FAQ</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.edit-faq-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('edit_faq_id').value = this.getAttribute('data-id');
                document.getElementById('edit_question').value = this.getAttribute('data-question');
                document.getElementById('edit_answer').value = this.getAttribute('data-answer');
                document.getElementById('edit_order').value = this.getAttribute('data-order');
            });
        });
    </script>

</body>

</html>