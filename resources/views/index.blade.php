<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Pago facil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous" />
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <br />
    <div class="container text-center shadow-lg p-3 mb-5 bg-body rounded">
        <div class="row">
            <div class="col">
                <span class="input-group-text">Company ID *</span>
                <input type="number" class="form-control" id="company_id" style="text-align: center;" />
            </div>
            <div class="col">
                <span class="input-group-text">Amount *</span>
                <input type="number" class="form-control" id="amount" style="text-align: center;" />
            </div>
            <div class="col">
                <span class="input-group-text">Product *</span>
                <input type="text" class="form-control" id="product_name" style="text-align: center;" />
            </div>
            <div class="col">
                <button type="button" id="btn_operation" class="btn btn-warning">Enviar</button>
            </div>
        </div>
    </div>
    <div class="container text-center shadow-lg p-3 mb-5 bg-body rounded">
        <div class="row">
            <div class="col">
                 Response:
             <div class="form-floating">
                <textarea class="form-control" id="response_id" style="height: 100px" disabled></textarea>
             </div>
            </div>
        </div>
    </div>
    <div class="container text-center shadow-lg p-3 mb-5 bg-body rounded">
        <div class="row">
            <div class="col">
                <table class="table table-success table-striped">
                    <thead>
                        <th>Company ID</th>
                        <th>Product ID</th>
                        <th>Company name</th>
                        <th>Product name</th>
                        <th>Amount</th>
                        <th>Date operation</th>
                    </thead>
                    <tbody id="tbody"></tbody>
                </table>
            </div>
        </div>
    </div>
</body>
<script>

    get_operations();

    function get_operations() {
        html = "";

        $.ajax({
            type: "POST",
            url: "get_operations",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                if (response.ok == 100) {
                    const obj = JSON.parse(response.data);
                    obj.forEach(function (objj, index) {
            
                        html +=
                            "<tr>" +
                            "<td>" + objj.company_id + "</td>" +
                            "<td>" + objj.product_id + "</td>" +
                            "<td>" + objj.company_name + "</td>" +
                            "<td>" + objj.product_name + "</td>" +
                            "<td>" + formatterPeso.format(objj.amount) + "</td>" +
                            "<td>" + objj.created_at + "</td>" +
                            "</tr>";
                    });
                } else {
                    html = "<tr>" + "<td colspan = 7>SIN DATOS ENCONTRADOS</td>" + "</tr>";
                }

                $("#tbody").html(html);
            },
        });
    }

    $( "#btn_operation" ).click(function() {
        company_id = $("#company_id").val();
        amount     = $("#amount").val();
        product_name = $("#product_name").val();

        request_id = {
            "company_id":company_id,
            "amount":amount,
            "company_id":product_name,
        };

        if( company_id && amount && product_name ){

            $.ajax({
            type: "POST",
            url: "operation",
            data: {
                _token: "{{ csrf_token() }}",
                company_id: company_id,
                amount: amount,
                product_name: product_name
            },
            success: function (response) {
                if (response.ok == 100) {
                    Swal.fire("Enviado", "Datos enviados", "success");
                    get_operations();
                    $("#response_id").text(response.data);

                    $("#company_id").val("");
                    $("#amount").val("");
                    $("#product_name").val("");

                } 
                else {
                    $("#response_id").text(response.data);

                        Swal.fire({
                        icon: "error",
                        title: "NO AUTORIZADO",
                        text: "ERRORES ENCONTRADOS",
                    });
                }
            },
        });

        }
        else{

                Swal.fire({
                    icon: "error",
                    title: "Campos vacios",
                    text: "Todos los campos son obligatorios",
                });
        }

    });

    const formatterPeso = new Intl.NumberFormat("es-mx", {
        style: "currency",
        currency: "MXN",
        minimumFractionDigits: 0,
    });

</script>
</html>