<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
<script src="{{ asset('/public/admin-elite/assets/node_modules/jquery/jquery-3.2.1.min.js') }}"></script>


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- Jquery for multi select or choose -->
<script src="{{ asset('/public/admin-elite/dist/js/chosen.jquery.js') }}"></script>

<!-- date picker jquery -->
<script src="{{ asset('/public/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>


<!-- Bootstrap popper Core JavaScript -->
<script src="{{ asset('/public/admin-elite/assets/node_modules/popper/popper.min.js') }}"></script>
<script src="{{ asset('/public/admin-elite/assets/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="{{ asset('/public/admin-elite/dist/js/perfect-scrollbar.jquery.min.js') }}"></script>
<!--Wave Effects -->
<script src="{{ asset('/public/admin-elite/dist/js/waves.js') }}"></script>
<!--Menu sidebar -->
<script src="{{ asset('/public/admin-elite/dist/js/sidebarmenu.js') }}"></script>
<!--Custom JavaScript -->
<script src="{{ asset('/public/admin-elite/dist/js/custom.min.js') }}"></script>

<!--stickey kit -->
<script src="{{ asset('/public/admin-elite/assets/node_modules/sticky-kit-master/dist/sticky-kit.min.js') }}">
</script>
<script src="{{ asset('/public/admin-elite/assets/node_modules/sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Sweet-Alert  -->
<script src="{{ asset('/public/admin-elite/assets/node_modules/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('/public/admin-elite/assets/node_modules/sweetalert/jquery.sweet-alert.custom.js') }}">
</script>

<!-- switchery  -->
<script src="{{ asset('/public/admin-elite/assets/node_modules/switchery/dist/switchery.min.js') }}"></script>

<!-- Morris graph chart -->
<script src="{{ asset('/public/admin-elite/assets/node_modules/morrisjs/morris.min.js') }}"></script>

<script src="{{ asset('/public/admin-elite/assets/node_modules/morrisjs/morris.js') }}"></script>
<script src="{{ asset('/public/admin-elite/assets/node_modules/morrisjs/raphael.min.js') }}"></script>

<!-- summernote  -->
<script src="{{ asset('/public/admin-elite/assets/node_modules/summernote/dist/summernote.min.js') }}"></script>
<!-- tagsinput  -->
<script
    src="{{ asset('/public/admin-elite/assets/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}">
</script>

<!-- This is Tree Menu JS  -->
<script src="{{ asset('/public/tree-menu/TreeMenu.js') }}"></script>

<!-- This is data table -->
<script src="{{ asset('/public/admin-elite/assets/node_modules/datatables/jquery.dataTables.min.js') }}"></script>

<!-- <script src="{{ asset('/public/js/tinymce/tinymce.min.js') }}"></script>
 <script>
    $(document).ready(function () {
        setTimeout(function () { //$(".message").hide('blind', {}, 500)); 
            $(".message").slideUp(1000).hide(1000);
        }, 5000);
        tinymce.init({
            selector: '.tinymce',
            forced_root_block: ''
        });
    });
</script> -->

<!-- <script type="text/javascript">

    function printPage(){
       print();
    }
//     $(document).ready(function() {
        
//         $('#printMe').click(function(){
// // alert('sdfs');
//              $("#printArea").print();
//         });

//         });
</script> -->


<script type="text/javascript">
    $(".chosen-select").chosen({
        search_contains: true
    });

</script>


<script>
    $(function() {

        // let top = '130px';
        // let left = '130px';

        // $('.add_datepicker').css({'left': left, 'top': top});

        
        $(".add_datepicker").datepicker({
            format: 'dd-mm-yyyy',
            changeMonth: true,
            changeYear: true,
            

        }).datepicker('setDate', 'today');

    var date = new Date();
    var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
    // var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

    $("#from_date_null").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
    });

    $("#from_date").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
        orientation: "bottom"
    });

    $("#from_date").datepicker("setDate", firstDay);

    $("#to_date").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
        orientation: "bottom"
    });

    $("#to_date").datepicker("setDate", 'today');

    $(".datepicker").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,

    });
    });

</script>

<script>
    $(document).ready(function() {
        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });

        var table = $('#dataTable').DataTable({
            "order": [
                [0, "asc"]
            ],
        });

        table.on('order.dt search.dt', function() {
            table.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        // $('[data-toggle="tooltip"]').tooltip();
    });

</script>

{{-- Script For GoBack Link --}}
<script type="text/javascript">
    function GoBack() {
        window.history.go(-1);
    }

</script>
