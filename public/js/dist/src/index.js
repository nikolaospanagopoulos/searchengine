(() => {
  // src/Telemetry.js
  function Telemetry() {
    if (document.querySelector(".site-container") || document.querySelector(".imageResults")) {
      var siteResults = document.querySelector(".site-container") || document.querySelector(".imageResults");
      console.log(123);
      siteResults.addEventListener("click", function(e) {
        if (e.target.tagName == "A" || e.target.tagName == "IMG") {
          e.preventDefault();
          console.log(e.target);
          console.log("link");
          var linkid = e.target.dataset.linkid;
          var url = e.target.href || e.target.dataset.url;
          console.log(linkid);
          var type = e.target.tagName == "IMG" ? "img" : "site";
          console.log(type);
          updateClicks(linkid, url, type);
        }
      });
    }
    async function updateClicks(linkid, url, type) {
      try {
        var rawRes = await fetch("/newTwitterAnalytics/search/updateClicks", {
          method: "POST",
          mode: "same-origin",
          credentials: "same-origin",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(`${type}:${linkid}`)
        });
        console.log(url);
        window.location.href = url;
      } catch (err) {
      }
    }
  }

  // src/index.js
  Telemetry();
})();
//# sourceMappingURL=index.js.map
