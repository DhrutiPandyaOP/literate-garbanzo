<div class="list-container video-list-container">
  <nav class="nav nav-tabs dashboard-tabs " role="tablist" id="templatesTabs" role="tablist">
    <!-- <div class="selector"></div> -->
    <a class="nav-item nav-link " style="border-top-left-radius: 5px!important;" id="nav-images-tab"
       data-toggle="tab" href="#nav-images" role="tab" aria-controls="nav-images" aria-selected="true">
      <!-- <img src="./image.svg" alt="" class="dashboard-tb-ic " id="image-ic" fill="#1980ff"> -->
      <svg version="1.1" class="dashboard-tb-ic " id="Capa_1" xmlns="http://www.w3.org/2000/svg"
           xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 384 384"
           style="enable-background:new 0 0 384 384;" xml:space="preserve" fill="#1980ff">
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
    <a class="nav-item nav-link " id="nav-videos-tab" style="border-top-right-radius: 5px!important;"
       data-toggle="tab" href="#nav-videos" role="tab" aria-controls="nav-videos" aria-selected="false">
      <!-- <img src="./video.svg" alt="" class="dashboard-tb-ic video-ic" id="video-ic"> -->
      <svg version="1.1" class="dashboard-tb-ic" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
           xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 407.51 407.51"
           style="enable-background:new 0 0 407.51 407.51;" xml:space="preserve" fill="#1980ff">
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
    <div class="tab-pane active" id="nav-images" role="tabpanel" aria-labelledby="nav-images-tab">

      <ul class="sub_cat_list mb-2">
        @foreach($image_pages as $row)

          <li class="categorylist ">
            <a data-category="{{ $row->sub_category_name or ''}}"
               class="list">{{ $row->sub_category_name or ''}}
              <svg class="icon-svg " version="1.1" x="0px" y="0px" width="10px" height="6px" viewBox="0 0 16 8">
                <path
                  d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                                                      	c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                </path>
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
    <div class="tab-pane" id="nav-videos" role="tabpanel" aria-labelledby="nav-videos-tab">
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
