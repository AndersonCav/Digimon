<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

if (\Auth::check()) {
    redirect('dashboard.php');
}

$error = null;

if (isPostRequest()) {
    if (!csrfIsValid($_POST['_csrf'] ?? null)) {
        $error = 'A sessão expirou. Recarregue a página e tente novamente.';
    } elseif ($conn === null) {
        $error = $dbError ?? 'Falha de conexão com o banco de dados.';
    } else {
        $username = inputString($_POST, 'username');
        $email = filter_var(inputString($_POST, 'email'), FILTER_VALIDATE_EMAIL);
        $senha = inputString($_POST, 'senha');

        if ($username === '' || $email === false || strlen($senha) < 6) {
            $error = 'Preencha os dados corretamente. A senha deve ter no mínimo 6 caracteres.';
        } else {
            $checkSql = 'SELECT id FROM usuarios WHERE email = ? LIMIT 1';
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param('s', $email);
            $checkStmt->execute();
            $exists = $checkStmt->get_result();

            if ($exists !== false && $exists->num_rows > 0) {
                $error = 'Este e-mail já está em uso.';
            } else {
                $senhaHashed = password_hash($senha, PASSWORD_DEFAULT);
                $sql = 'INSERT INTO usuarios (username, email, senha) VALUES (?, ?, ?)';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sss', $username, $email, $senhaHashed);

                if ($stmt->execute()) {
                    flashSet('success', 'Cadastro realizado com sucesso. Faça login para continuar.');
                    redirect('login.php');
                }

                $error = 'Não foi possível concluir o cadastro agora.';
            }
        }
    }
}

include __DIR__ . '/../templates/header.php';
?>

<section class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h2 class="h4 mb-3">Criar conta</h2>

                <?php if ($error !== null): ?>
                    <div class="alert alert-danger" role="alert"><?php echo h($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="register.php" novalidate>
                    <?php echo csrfField(); ?>

                    <div class="mb-3">
                        <label for="username" class="form-label">Nome de usuário</label>
                        <input type="text" class="form-control" id="username" name="username" required maxlength="80">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required minlength="6">
                    </div>

                    <button type="submit" class="btn btn-success w-100">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>