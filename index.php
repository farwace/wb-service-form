<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>wb</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

</head>
<body>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h1 class="mb-3">Место хранения</h1>
            <form>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="location" class="form-label">Место хранения</label>
                        <input type="text" class="form-control" autofocus id="location" name="location" required>
                    </div>
                    <div id="barcodes">
                        <div class="col-md-12">
                            <label for="barcode1" class="form-label">Баркод</label>
                            <input type="text" class="form-control" id="barcode1" name="barcode[]">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex">
                            <button class="btn btn-warning ms-auto">+ Добавить баркод</button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="palette" class="form-label">Палета</label>
                        <input type="text" class="form-control" id="palette" name="palette">
                    </div>
                    <div class="col-md-6">
                        <label for="wb-box" class="form-label">WB Короб</label>
                        <input type="text" class="form-control" id="wb-box" name="wb-box">
                    </div>
                    <div class="col-md-6">
                        <label for="count" class="form-label">Количество</label>
                        <input type="number" class="form-control" id="count" name="count">
                    </div>
                    <div class="col-md-6">
                        <label for="product-name" class="form-label">Наименование товара</label>
                        <input type="text" class="form-control" id="product-name" name="product-name">
                    </div>

                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-dark w-100 fw-bold" >Отправить</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>