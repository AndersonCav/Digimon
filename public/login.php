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
        $email = filter_var(inputString($_POST, 'email'), FILTER_VALIDATE_EMAIL);
        $senha = inputString($_POST, 'senha');

        if ($email === false || $senha === '') {
            $error = 'Informe um e-mail válido e a senha.';
        } else {
            $sql = 'SELECT id, username, senha FROM usuarios WHERE email = ? LIMIT 1';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result ? $result->fetch_assoc() : null;

            if (is_array($user) && password_verify($senha, (string) $user['senha'])) {
                \Auth::login($user);
                flashSet('success', 'Login realizado com sucesso.');
                redirect('dashboard.php');
            }

            $error = 'E-mail ou senha incorretos.';
        }
    }
}

include __DIR__ . '/../templates/header.php';
?>

<section class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card auth-card reveal-up">
            <div class="card-body p-4">
                <p class="hero-eyebrow mb-1">Acesso</p>
                <h2 class="h4 mb-3">Entrar na sua conta</h2>

                <?php if ($error !== null): ?>
                    <div class="alert alert-danger" role="alert"><?php echo h($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="login.php" novalidate>
                    <?php echo csrfField(); ?>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>

                <p class="mt-3 mb-0 text-muted">Ainda não tem conta? <a href="register.php">Criar conta</a>.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>