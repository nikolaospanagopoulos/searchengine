(() => {
  // src/Telemetry.js
  function Telemetry() {
    if (document.querySelector(".site-container")) {
      var siteResults = document.querySelector(".site-container");
      siteResults.addEventListener("click", function(e) {
        if (e.target.tagName == "A") {
          e.preventDefault();
          console.log("link");
        }
      });
    }
  }
})();
//# sourceMappingURL=Telemetry.js.map
