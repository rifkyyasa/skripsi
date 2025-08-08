<footer class="ftco-footer ftco-bg-dark">
      <div class="container">
        <div class="row mb-5">
          <div class="col-md-8">
            <div class="row">
              <div class="col-md">
                <div class="ftco-footer-widget mb-4">
                  <h2 class="ftco-heading-2" style="font-weight: bold;">Digital Learning SMP PGRI CIKARANG BARAT </h2>
                  <p class="text-justify">Karena Pada pembejaran digital ini siswa akan dibimbing jarak jauh oleh guru mata pelajarannya masing-masing melalui website yang telah disiapkan, siswa/i akan diberikan materi pelajaran yang berupa modul sesuai dengan kurikulum yang diterapkan, kemudian di akhir setiap modul ada test yang diberikan bagi setiap siswa/i yang bertujuan untuk mengukur ketercapaian belajar siswa</p>
                  
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="ftco-footer-widget mb-4">
              <p>&copy; SMP PGRI CIKARANG BARAT </p>
              <script type="text/javascript"> //<![CDATA[
                var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
                document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
              //]]>
                
              </script>
              <script language="JavaScript" type="text/javascript">
                TrustLogo("https://www.positivessl.com/images/seals/positivessl_trust_seal_sm_124x32.png", "POSDV", "none");
              </script>
            </div>
          </div>
        </div>
        <!-- <div class="row">
          <div class="col-md text-left">
            <p>&copy; SMK KAWULA INDONESIA </p>
          </div>
        </div> -->
      </div>
    </footer>


    <!-- loader -->
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#4586ff"/></svg></div>

    
    <script src="dist/js-landing/jquery.min.js"></script>
    <script src="dist/js-landing/popper.min.js"></script>
    <script src="dist/js-landing/bootstrap.min.js"></script>
    <script src="dist/js-landing/jquery.easing.1.3.js"></script>
    <script src="dist/js-landing/jquery.waypoints.min.js"></script>
    <script src="dist/js-landing/owl.carousel.min.js"></script>
    <script src="dist/js-landing/jquery.animateNumber.min.js"></script>
    <script src="plugin/bs_growl/jquery.bootstrap-growl.js"></script>
    
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
    <script src="dist/js-landing/google-map.js"></script>

    <script src="dist/js-landing/main.js"></script>
    <script type="text/javascript">
      var TxtType = function(el, toRotate, period) {
        this.toRotate = toRotate;
        this.el = el;
        this.loopNum = 0;
        this.period = parseInt(period, 10) || 2000;
        this.txt = '';
        this.tick();
        this.isDeleting = false;
      };

      TxtType.prototype.tick = function() {
          var i = this.loopNum % this.toRotate.length;
          var fullTxt = this.toRotate[i];

          if (this.isDeleting) {
          this.txt = fullTxt.substring(0, this.txt.length - 1);
          } else {
          this.txt = fullTxt.substring(0, this.txt.length + 1);
          }

          this.el.innerHTML = '<span class="wrap">'+this.txt+'</span>';

          var that = this;
          var delta = 200 - Math.random() * 100;

          if (this.isDeleting) { delta /= 2; }

          if (!this.isDeleting && this.txt === fullTxt) {
          delta = this.period;
          this.isDeleting = true;
          } else if (this.isDeleting && this.txt === '') {
          this.isDeleting = false;
          this.loopNum++;
          delta = 500;
          }

          setTimeout(function() {
          that.tick();
          }, delta);
      };

      window.onload = function() {
          var elements = document.getElementsByClassName('typewrite');
          for (var i=0; i<elements.length; i++) {
              var toRotate = elements[i].getAttribute('data-type');
              var period = elements[i].getAttribute('data-period');
              if (toRotate) {
                new TxtType(elements[i], JSON.parse(toRotate), period);
              }
          }
          // INJECT CSS
          var css = document.createElement("style");
          css.type = "text/css";
          css.innerHTML = ".typewrite > .wrap { border-right: 0.08em solid #fff}";
          document.body.appendChild(css);
      };
    </script>
    
  </body>
</html>