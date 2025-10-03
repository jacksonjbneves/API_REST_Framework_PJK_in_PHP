<h1>Página <?= $titulo ?? '' ?> - Nome: <?= $usuario['nome']; ?></h1>


<div class="d-flex justify-content-center align-items-center my-5 p-0">
    <div class="container text-center border rounded-5 shadow">
        <h4 class="text-center mt-4">ArchSofts</h4>
        <h1 class="text-center">Página em Manutenção</h1>
        <div class="d-flex justify-content-center">
            <dotlottie-wc src="https://lottie.host/313e3db2-c221-472f-88c0-0d4ce7b75e8b/S7S8ks5P34.lottie" speed="1" style="width: 300px; height: 300px" mode="forward" loop autoplay></dotlottie-wc>
        </div>
    </div>
</div>

<?php 
//$elem = [1,2,3,4,5,6,7,8,9,10];
$elem = [1,2,3];
foreach($elem as $e){?>

<div class="card" style="width: 18rem;">
  <img src="..." class="card-img-top" alt="...">
  <div class="card-body">
    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
  </div>
</div>

<?php } ?>