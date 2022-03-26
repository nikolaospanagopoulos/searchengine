(() => {
  // src/Telemetry.js
  function Telemetry() {
    if (document.querySelector(".site-container") || document.querySelector(".imageResults")) {
      var siteResults = document.querySelector(".site-container") || document.querySelector(".imageResults");
      siteResults.addEventListener("click", function(e) {
        if (e.target.tagName == "A" || e.target.tagName == "IMG") {
          e.preventDefault();
          var linkid = e.target.dataset.linkid;
          var url = e.target.href || e.target.dataset.url;
          var type = e.target.tagName == "IMG" ? "img" : "site";
          updateClicks(linkid, url, type);
        }
      });
    }
    async function updateClicks(linkid, url, type) {
      try {
        window.open(url, "_blank").focus();
        var rawRes = await fetch("/search/updateClicks", {
          method: "POST",
          mode: "same-origin",
          credentials: "same-origin",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(`${type}:${linkid}`)
        });
      } catch (err) {
      }
    }
  }

  // src/index.js
  Telemetry();
})();
//# sourceMappingURL=index.js.map
