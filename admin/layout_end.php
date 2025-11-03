    </section> t
  </main> 

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <script>
    $(document).ready(function () {
      $('table.data').DataTable({
        language: {
          url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        },
        pageLength: 10,
        responsive: true
      });
    });

    const sidebar = document.querySelector('.ss-sidebar');
    const main = document.querySelector('.ss-main');
    let sidebarOpen = true;

    function toggleSidebar() {
      if (window.innerWidth < 900) {
        sidebarOpen = !sidebarOpen;
        if (sidebarOpen) {
          sidebar.style.left = '0';
          main.style.marginLeft = '240px';
        } else {
          sidebar.style.left = '-240px';
          main.style.marginLeft = '0';
        }
      }
    }

    document.addEventListener('click', (e) => {
      if (window.innerWidth < 900 && sidebarOpen && !sidebar.contains(e.target)) {
        toggleSidebar();
      }
    });
  </script>
</body>
</html>
