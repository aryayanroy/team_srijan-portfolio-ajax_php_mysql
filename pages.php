<?php
    include_once "php/session.php";
    include_once "php/admin.php";
    include_once "php/imagekit-config.php";
    
    if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["action"])){
        $action = $_POST["action"];
        $response = [false];
        $img_dir = "heros";
        if($action == 0){
            $sql = $conn->prepare("SELECT * FROM pages");
            try{
                $sql->execute();
                $response[0] = true;
                if($sql->rowCount() >= 1){
                    $response[1] = $sql->fetchAll(PDO::FETCH_NUM);
                }
                $response[2] = image($img_dir, 80, 45);
            }catch(PDOException $e){
                $response[1][] = "Couldn't fetch records.";
                $response[2][] = $e->getMessage();
            }
        }elseif($action == 1){
            $image = time();
            $upload = upload($_FILES["hero"], $image, $img_dir);
            if($upload === true){
                $sql = $conn->prepare("INSERT INTO pages (page, title, image, overview) VALUES (?, ?, ?, ?)");
                $sql->bindParam(1, $_POST["page"], PDO::PARAM_STR);
                $sql->bindParam(2, $_POST["title"], PDO::PARAM_STR);
                $sql->bindParam(3, $image, PDO::PARAM_INT);
                $sql->bindParam(4, $_POST["overview"], PDO::PARAM_STR);
                try{
                    $sql->execute();
                    $response[0] = true;
                    $response[1][] = "Page added successfully.";
                }catch(PDOException $e){
                    $response[1][] = "Couldn't add page.";
                    $response[2][] = $e->getMessage();
                }
            }else{
                $response[1][] = "Couldn't upload image.";
                $response[2][] = $upload;
            }
        }elseif($action == 3){
            $sql = $conn->prepare("DELETE FROM pages WHERE id = ?");
            $sql->bindParam(1, $_POST["id"], PDO::PARAM_INT);
            try{
                $sql->execute();
                $response[0] = true;
                $response[1][] = "Page removed successfully.";
            }catch(PDOException $e){
                $response[1][] = "Couldn't remove page.";
                $response[2][] = $e->getMessage();
            }
        }
        include_once "php/response.php";
    }
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <?php
        include_once "php/head.php";
        include_once "php/admin-head.php";
    ?>
    <title>Pages | Team Srijan</title>
</head>
<body class="d-flex flex-column min-vh-100 bg-body-secondary">
    <?php include_once "php/admin-header.php"; ?>
    <main class="flex-grow-1 py-3">
        <div class="container-xxl">
            <div class="row">
                <div class="col-3">
                    <aside class="border bg-white rounded">
                        <nav class="p-3 nav nav-pills flex-column">
                            <a href="personal" class="nav-link">Account</a>
                            <a href="pages" class="nav-link active">General</a>
                            <a href="sponsor" class="nav-link">Sponsorship</a>
                            <a href="milestone" class="nav-link">Legacy</a>
                            <a href="admins" class="nav-link">Admins</a>
                        </nav>
                    </aside>
                </div>
                <div class="col-9">
                    <article class="p-3 border bg-white rounded">
                        <h3 class="pb-2 border-bottom">General</h3>
                        <nav class="nav nav-underline nav-fill">
                            <a href="pages" class="nav-link active">Pages</a>
                            <a href="home" class="nav-link">Home</a>
                            <a href="admin-updates" class="nav-link">Updates</a>
                            <a href="alert" class="nav-link">Alert</a>
                            <a href="links" class="nav-link">Links</a>
                            <a href="admin-gallery" class="nav-link">Gallery</a>
                        </nav>
                        <div class="my-3 text-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-page"><i class="fa-solid fa-plus me-2"></i><span>Add page</span></button>
                        </div>
                        <table id="data-table" class="table table-sm table-striped">
                            <tr>
                                <th class="text-center" colspan="2">Page</th>
                                <th class="text-center">Title</th>
                                <th class="text-center">Hero</th>
                                <th class="text-center">Overview</th>
                            </tr>
                        </table>
                    </article>
                </div>
            </div>
        </div>
    </main>
    <?php include_once "php/footer.php"; ?>
    <div id="add-page" class="modal fade">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" id="input-form" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Page Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class='ratio ratio-21x9'>
                                <img src="" alt="Hero" id="hero-image" class="img-thumbnail object-fit-cover">
                                <div>
                                    <input type="file" id="hero" name="hero" class="d-none" accept="image/*">
                                    <label for="hero" class="btn btn-primary position-absolute top-50 start-50 translate-middle shadow"><i class="fa-solid fa-camera fa-lg me-2"></i><span>Upload Image</span></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" id="page" name="page" class="form-control" placeholder="Page name" autocomplete="off" value="" required>
                                <label for="page">Page name</label>
                            </div>
                            <span class="form-text">Eg: www.teamsrijan.com/<span class="fw-bold text-danger">home</span>.php</span>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" id="title" name="title" class="form-control" placeholder="Title" autocomplete="off" value="" required>
                                <label for="title">Title</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea id="overview" name="overview" class="form-control" placeholder="Overview" style="height: 100px; resize:none" autocomplete="off"></textarea>
                                <label for="overview">Overview</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save page</button>
                </div>
            </form>
        </div>
    </div>
    <?php include_once "php/loading.php"; ?>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="assets/public/js/admin.js"></script>
<script>
    $(document).ready(function(){
        $("#hero").change(function(){
            const file = this.files[0];
            if(file){
                let reader = new FileReader();
                reader.onload = function(event){
                    $("#hero-image").attr("src", event.target.result);
                }
                reader.readAsDataURL(file);
            }
        })

        function set_data(){
            load_data([], function(response){
                var data = JSON.parse(response);
                if(data[0]){
                    var table = $("#data-table");
                    table.find("tr:not(:first)").remove();
                    if(data[1]){
                        var new_row;
                        $.each(data[1], function(_, row){
                            new_row = $("<tr>");
                            $.each(row, function(i, col){
                                if(i==0){
                                    new_row.append("<td class='text-center'><button type='button' class='btn btn-link link-danger btn-sm delete-btn' value='"+col+"'><i class='fa-solid fa-trash'></i></button></td>")
                                }else if(i==3){
                                    new_row.append("<td class='text-center'><img src='"+data[2]+"/"+col+"'></td>")
                                }else{
                                    new_row.append("<td>"+col+"</td>")
                                }
                            })
                            table.append(new_row);
                        })
                    }else{
                        null_rows(table)
                    }
                }else{
                    response_messages(data[1], data[2]);
                }
            });
        }

        set_data();

        $("#input-form").submit(function(e){
            e.preventDefault();
            var form = $(this);
            var data = new FormData(this);
            var btn = find_btn(form);
            submit_multipart(btn, data, 1, function(response){
                var data = JSON.parse(response);
                if(data[0]){
                    form[0].reset();
                    $("#hero-image").attr("src", null);
                    $("#add-page").modal("hide");
                    set_data();
                }
                response_messages(data[1], data[2]);
            })
        })

        $(document).on("click", ".delete-btn", function(){
            submit_delete($(this), function(response){
                var data = JSON.parse(response);
                if(data[0]){
                    set_data();
                }
                response_messages(data[1], data[2]);
            })
        })
    })
</script>
</html>