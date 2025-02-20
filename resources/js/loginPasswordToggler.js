// Login Form Password Toggler
// document.getElementById('togglePassword').addEventListener('click', function (e) {
//     const passwordInput = e.target.closest('.input-group').querySelector('input');
//     const icon = document.getElementById('eyeicon');
//     if (passwordInput.type === 'password') {
//         passwordInput.type = 'text';
//         icon.classList.remove('bi', 'bi-eye');
//         icon.classList.add('bi', 'bi-eye-slash');
//     } else {
//         passwordInput.type = 'password';
//         icon.classList.remove('bi', 'bi-eye-slash');
//         icon.classList.add('bi', 'bi-eye');
//     }
// });

$("#togglePassword").on("click", function() {
    if ($("#userPassword").attr("type") == "password") {
        $("#userPassword").attr("type", "text");
        $("#eyeicon").removeClass(['bi', 'bi-eye']);
        $("#eyeicon").addClass(['bi', 'bi-eye-slash']);
    } else {
        $("#userPassword").attr("type", "password");
        $("#eyeicon").removeClass(['bi', 'bi-eye-slash']);
        $("#eyeicon").addClass(['bi', 'bi-eye']);
    }
});