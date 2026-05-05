describe('Login Flow', () => {
  it('harus berhasil login sebagai pelanggan', () => {
    cy.visit('/auth');

    // INFO: Ubah 'pelanggan_test' dan 'password123' sesuai dengan akun di database lokal Anda.
    // Gunakan selector ID form agar lebih spesifik
    cy.get('#loginForm input[name="username"]').type('alterjoo');
    cy.get('#loginForm input[name="password"]').type('alter123');


    cy.get('form#loginForm').submit();

    // Pastikan diredirect ke halaman dashboard setelah sukses login
    cy.url().should('include', '/dashboard');
  });
});
