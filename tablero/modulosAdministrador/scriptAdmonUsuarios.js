
function abrirFormularioInsertarUsuario() {
    var url = 'admonUsuariosInsertarUsuario.php';
    window.open(url, "Insertar Usuario", "width=700,height=600");
}
function abrirEditorUsuario(username) {
    var url = "editarUsuarioAdmonUsuarios.php?username=" + encodeURIComponent(username);
    window.open(url, '_blank', 'height=500,width=800,left=200,top=200,resizable=yes');
}
