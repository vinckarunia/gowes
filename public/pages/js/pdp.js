function getDataByWindowUrlKey(){
    let windowUrl = $(location).attr('href'); 
    let windowUrlKey = windowUrl.replace(/\/\s*$/, "").split('/').pop();
    let url = baseUrl+'/api/bicycle/'+windowUrlKey;
    
    axios.get(url,{},apiHeaders)
    .then(function (response) {
      console.log('[DATA] response..',response.data);
      let template = '';

      $('.product-img-main-href').attr('href',response.data.image);
      $('.product-img-main-src').attr('src',response.data.image);
      $('#product-name').html(response.data.model);
      $('#product-price').html('IDR '+parseFloat(response.data.price).toLocaleString());
      $('#product-description').html(response.data.description);
      $('#product-manufacturer').html(response.data.manufacturer);
      $('#product-nation').html('Produced in '+response.data.nation);

      // START -- note, unless you have these data in a database structure, here we are hardcoding them for display purposes
        // review
        let stars = randomIntFromInterval(1,5);
        template = '';
        for (let index = 0; index < 5; index++) {
          template += '<i class="'+(index<stars?'yellow':'')+' icon_star"></i>';
        }
        $('#product-review-stars').html(template);
        $('#product-review-body-count').html(randomIntFromInterval(1,1000)+' customer review');
        // status stock
        let statusStock = randomIntFromInterval(0,1);
        $('#product-status-stock').addClass(statusStock?'in-stock':'out-of-stock');
        $('#product-status-stock').html(statusStock?'<p>Available: <span>In stock</span></p>':'<p>Available: <span>Out of stock</span></p>');
        if(!statusStock){
          $('.product-add-to-cart').hide();
          $('.product-add-to-cart-is-disabled').show();
        }
        // tag
        let collectionOfTag = ['Bicycle', 'BMX', 'City Bikes', 'Hybrid Bikes', 'Cyclocross Bikes','Fitness Bikes', 'Gravel Bikes', 'Kids Bikes', 'Mountain Bikes', 'Road Bikes','Randonneur Bikes', 'Touring Bikes', 'Triathlon Bikes'];
        let selectedTags    = collectionOfTag.sort(() => .5 - Math.random()).slice(0, 4); // only get 4, randomly, from collectionOfTag
        template = '';
        for (let index = 0; index < selectedTags.length; index++) {
          template += '<a href="#">'+selectedTags[index]+'</a>'+(index!=selectedTags.length-1?', ':'');

        }
        $('#product-tags').html(template);
      // END ----
    })
    .catch(function (error) {
      console.log('[ERROR] response..',error.code);
      if(error.code == "ERR_BAD_REQUEST"){
        Swal.fire({
          position: "top-end",
          icon: "warning",
          title: "Maaf",
          html: "Produk tidak ditemukan",
          showConfirmButton: false,
          timer: 5000
        });
      }else{
        Swal.fire({
          icon: 'error',
          width: 600,
          title: "Error",
          html: error.message,
          confirmButtonText: 'Ya',
        });
      }
    });
  }
  
  $(function () {
    getDataByWindowUrlKey();
  });