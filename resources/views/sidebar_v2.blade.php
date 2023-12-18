
<div class="list-container video-list-container">
<div class="template-search-container">
  <input type="text" class="template-search-input" id="searchInput" placeholder="Search Templates..." onkeypress="
                  enterKeyPressed(event)" autocomplete="off" onkeyup="wordTyped(event.target.value)">  
  <span class="input-group-text c-template-search-btn" onclick="onSearchClick()">
                  <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M18.5821 16.3522H17.4003L16.9914 15.9434C18.4483 14.2562 19.3253 12.0635 19.3253 9.66269C19.3253 4.3259 14.9994 0 9.66269 0C4.32597 0 0 4.3259 0 9.66269C0 14.9995 4.3259 19.3254 9.66269 19.3254C12.0635 19.3254 14.2562 18.4483 15.9434 16.9989L16.3522 17.4077V18.5821L23.785 26L26 23.785L18.5821 16.3522ZM9.66269 16.3522C5.9686 16.3522 2.97315 13.3568 2.97315 9.66269C2.97315 5.9686 5.9686 2.97315 9.66269 2.97315C13.3568 2.97315 16.3522 5.9686 16.3522 9.66269C16.3522 13.3568 13.3568 16.3522 9.66269 16.3522Z"
                      fill="#0069FF" />
                  </svg>
                </span>
                <div class="auto-complete-drop-down template-auto-com" id="autoCompleteDrop">
                </div>
  </div>
  <nav class="nav nav-tabs dashboard-tabs " role="tablist" id="templatesTabs" role="tablist">
    <!-- <div class="selector"></div> -->
    <a class="nav-item nav-link {{ $all_pages['image_class_name'] }}" style="border-top-left-radius: 6px!important;" id="nav-images-tab"
       data-toggle="tab" href="#nav-images" role="tab" aria-controls="nav-images" aria-selected="true">
      <!-- <img src="./image.svg" alt="" class="dashboard-tb-ic " id="image-ic" fill="#1980ff"> -->
      <svg version="1.1" class="dashboard-tb-ic " id="Capa_1" xmlns="http://www.w3.org/2000/svg"
           xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 384 384"
           style="enable-background:new 0 0 384 384;" xml:space="preserve" fill="#0069FF">
                <g>
                  <g>
                    <path d="M341.333,0H42.667C19.093,0,0,19.093,0,42.667v298.667C0,364.907,19.093,384,42.667,384h298.667
                                       C364.907,384,384,364.907,384,341.333V42.667C384,19.093,364.907,0,341.333,0z M42.667,320l74.667-96l53.333,64.107L245.333,192
                                       l96,128H42.667z"/>
                  </g>
                </g>

              </svg>

      <span class="dashboard-tb-txt">Image</span>
    </a>
    <a class="nav-item nav-link {{ $all_pages['video_class_name'] }}" id="nav-videos-tab" style="border-top-right-radius: 6px!important;"
       data-toggle="tab" href="#nav-videos" role="tab" aria-controls="nav-videos" aria-selected="false">
      <!-- <img src="./video.svg" alt="" class="dashboard-tb-ic video-ic" id="video-ic"> -->
      <svg version="1.1" class="dashboard-tb-ic" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
           xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 407.51 407.51"
           style="enable-background:new 0 0 407.51 407.51;" xml:space="preserve" fill="#0069FF">
                <g>
                  <g>
                    <g>
                      <polygon points="167.184,301.453 235.624,261.224 167.184,220.996 			"/>
                      <path d="M360.49,112.327H47.02c-25.969,0-47.02,21.052-47.02,47.02v198.531c0,25.969,21.052,47.02,47.02,47.02H360.49
				c25.969,0,47.02-21.052,47.02-47.02V159.347C407.51,133.378,386.458,112.327,360.49,112.327z M264.628,265.658
				c-0.961,1.926-2.523,3.487-4.448,4.449l-99.788,58.514c-1.534,1.058-3.362,1.606-5.224,1.567l-4.702-1.567
				c-2.95-1.941-4.565-5.371-4.18-8.882V202.71c-0.385-3.51,1.229-6.941,4.18-8.882c3-2.022,6.926-2.022,9.927,0l99.788,58.514
				C265.085,254.791,267.076,260.752,264.628,265.658z"/>
                      <path d="M53.812,80.98H354.22c5.771,0,10.449-4.678,10.449-10.449s-4.678-10.449-10.449-10.449H53.812
				c-5.771,0-10.449,4.678-10.449,10.449S48.041,80.98,53.812,80.98z"/>
                      <path d="M98.22,23.51H309.29c5.771,0,10.449-4.678,10.449-10.449S315.061,2.612,309.29,2.612H98.22
				c-5.771,0-10.449,4.678-10.449,10.449S92.45,23.51,98.22,23.51z"/>
                    </g>
                  </g>
                </g>

              </svg>

      <span class="dashboard-tb-txt">Video</span>
    </a>
  </nav>
  @php
   $image_pages = $all_pages['image_pages'];
  $video_pages = $all_pages['video_pages'];
  @endphp
  {{--                                {{ dd($redis_result) }}--}}
  <div id="nav-tabContent">
    <div class="tab-pane {{ $all_pages['image_class_name'] }}" id="nav-images" role="tabpanel" aria-labelledby="nav-images-tab">

      <ul class="sub_cat_list mb-2">
        @foreach($image_pages as $row)

          <li class="categorylist ">
            <a data-category="{{ $row->sub_category_name or ''}}"
               class="list">{{ $row->sub_category_name or ''}}

              <svg class="icon-svg " width="13" height="7" viewBox="0 0 13 7" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M12.8109 0.19096C12.5546 -0.059747 12.1363 -0.059747 11.88 0.19096L6.5032 5.4558L1.11971 0.19096C0.863349 -0.059747 0.445082 -0.059747 0.188725 0.19096C-0.0676321 0.441667 -0.0676321 0.844117 0.181979 1.09482L0.188725 1.10142L6.03097 6.8149C6.28732 7.05901 6.69884 7.05901 6.96195 6.8149L12.8042 1.10142C13.0673 0.850715 13.0673 0.441667 12.8109 0.19096Z" fill="#575D68"/>
</svg>

            </a>

            <ul class="sublist" data-category="{{ $row->sub_category_name or ''}}">
              <li id="{{ $row->sub_category_name or ''}}" class="sub-category"
                  data-category="{{ $row->sub_category_name or ''}}">
                <a class="subcategorylink" href="{{$row->page_url}}"
                   data-name="{{ $row->sub_category_name or ''}}">All {{ $row->sub_category_name or ''}}</a>
              </li>
              @if(isset($row->sub_page) && count($row->sub_page) > 0)
                @foreach($row->sub_page as $sub_page)
                  <li id="{{ $sub_page->catalog_path or ''}}" class="sub-category"
                      data-category="{{ $sub_page->catalog_name or ''}}">
                    <a class="subcategorylink" href="{{$sub_page->page_url}}"
                       data-name="{{ $row->sub_category_name or ''}}"> {{ $sub_page->catalog_name or ''}}</a>
                  </li>
                @endforeach
              @endif
            </ul>
          </li>
        @endforeach
      </ul>
    </div>
    <div class="tab-pane {{ $all_pages['video_class_name'] }}" id="nav-videos" role="tabpanel" aria-labelledby="nav-videos-tab">
      <ul id="sub_cat_list_video" class="sub_cat_list_video">
        @foreach($video_pages as $row)
          <li id="{{ $row->tag_title or '' }}" class="categorylist">
            <a href="{{ $row->page_url or ''}}" data-category="{{ $row->tag_title or ''}}" class="list">{{ $row->tag_title or ''}}</a>
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
