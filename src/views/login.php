<section class="container-full xborder xborder-danger login d-flex align-items-center">
    <div class="container bg-dark text-light border border-success xshadow rounded p-5 " style="max-width: 400px;">
        <h2 class="mb-3">Login</h2>
    
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>login">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
    
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" name="senha" id="senha" class="form-control" required>
            </div>
    
            <button type="submit" class="btn btn-success w-100">Entrar</button>

            <?php 
            //echo $hash = password_hash("1290115", PASSWORD_DEFAULT);
            //$2y$10$6MT1HRyDtDHVuuryi4n2LeBjd6gdAkF3AGq5XVo/0iUejdEvrZ8Uu
            ?>
        </form>
    </div>
</section>

<style>
    .footer{        
        height: 100%;
    }
</style>
