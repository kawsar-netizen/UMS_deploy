<style type="text/css">
	.entry-meta ul li a,i{
    
    color: gray;
}

.entry-meta ul {
    display: flex;
    flex-wrap: wrap;
    list-style: none;
    padding: 0;
    margin: 0;
}

</style>



  <div class="modal-body">
  	

  	
   
    

     <form action="" method="POST">
          

          <input type="hidden" name="hidden_id" id="hidden_id" value="<?php  echo $single_fetch_data->id; ?>">
          
          <table class="table table-bordered" width="80%">
              <tr>
                  <th>System Id </th>
                  <td><input type="text" class="form-control" name="system_id" id="system_id" value="<?php echo $single_fetch_data->sys_id;?>"></td>
              </tr>

             
             {{-- Editing code by Kawsar --}}
              <tr>
                  <th>System Name</th>
                  <td><input type="text" class="form-control" name="system_name" id="system_name" value="<?php echo $single_fetch_data->system_name;?>"></td>

              </tr>

              <tr>
                <th>Status</th>
                <td>
                  <select class="form-control" name="status" id="status">

                      <option <?php if ($single_fetch_data->sys_status=="1") {
                        
                        echo "selected";

                      } ?> value="1">Active</option>

                      <option <?php if ($single_fetch_data->sys_status=="0") {
                        
                        echo "selected";
                        
                      } ?> value="0">Dective</option>

                  </select>
                </td>
              </tr>

             

          </table>

           


      </form>



      

  </div>