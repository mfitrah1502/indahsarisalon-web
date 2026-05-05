describe('Dashboard Pelanggan', () => {
  beforeEach(() => {
    // Login sebelum setiap test
    cy.visit('/auth');
    cy.get('input[name="username"]').type('alterjoo');
    cy.get('input[name="password"]').type('alter123');
    cy.get('form#loginForm').submit();
  });

  it('harus memuat elemen kunci di dashboard', () => {
    cy.visit('/dashboard');

    // Memastikan elemen dashboard utama termuat
    cy.contains('Dashboard', { matchCase: false }).should('be.visible');
  });
});
