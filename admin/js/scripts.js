document.addEventListener("DOMContentLoaded", function () {
  const departmentSelect = document.querySelector('select[name=department]');

  if (departmentSelect) {
    departmentSelect.addEventListener("change", function () {
      const department_id = this.value;

      fetch('ajax.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          action: "get_positions_by_department",
          department_id: department_id
        })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log(data.data);

        var txt = "<option =''>any</option>"

        data.data.forEach(pos => {
            txt += "<option value='" + pos.id + "'>" + pos.name + "</option>"
        });

        document.querySelector('select[name=positions]').innerHTML = txt;

      })
      .catch(error => {
        console.error('AJAX Error:', error);
      });
    });
  }
});