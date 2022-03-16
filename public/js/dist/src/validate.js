(() => {
  // src/validate.js
  var form = document.querySelector("form");
  var formInput = document.querySelector("input");
  form.addEventListener("submit", function(e) {
    if (formInput.value.length == 0) {
      e.preventDefault();
    }
  });
})();
//# sourceMappingURL=validate.js.map
