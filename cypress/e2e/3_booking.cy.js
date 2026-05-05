describe('Booking Flow', () => {
  beforeEach(() => {
    // Login
    cy.visit('/auth');
    cy.get('input[name="username"]').type('alterjoo');
    cy.get('input[name="password"]').type('alter123');
    cy.get('form#loginForm').submit();
  });

  it('harus dapat melihat dan memilih treatment untuk dibooking', () => {
    cy.visit('/booking');

    // Pastikan area list treatment muncul
    cy.get('#treatmentList').should('exist');

    // Klik salah satu treatment card (yang pertama muncul)
    cy.get('#treatmentList').then($list => {
      if ($list.find('.treatment-card').length > 0) {
        cy.get('.treatment-card').first().click();

        // Pastikan URL berubah ke halaman pemilihan jam/stylist
        cy.url().should('include', '/booking/select');

        // Catatan: Pengujian dihentikan di sini agar tidak membuat data booking sungguhan
        // di database lokal Anda (bisa diteruskan jika diperlukan).
      } else {
        cy.log('Tidak ada treatment yang tersedia saat ini.');
      }
    });
  });
});
