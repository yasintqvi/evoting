 <!-- Sidenav Menu Start -->
 <div class="sidenav-menu">

     <!-- Brand Logo -->
     <a href="/" class="logo">
         <span class="logo-light">
             <span class="logo-lg"><img src="/assets/img/logo.webp" alt="logo" style="width: 3rem; height: 3rem"></span>
             <span class="logo-sm"><img src="/assets/img/logo.webp" alt="small logo" style="width: 3rem; height: 3rem"></span>
         </span>

         <span class="logo-dark">
             <span class="logo-lg"><img src="/assets/img/logo.webp" alt="dark logo" style="width: 3rem; height: 3rem"></span>
             <span class="logo-sm"><img src="/assets/img/logo.webp" alt="small logo" style="width: 3rem; height: 3rem"></span>
         </span>
     </a>

     <!-- Sidebar Hover Menu Toggle Button -->
     <button class="button-sm-hover">
         <i class="ti ti-circle align-middle"></i>
     </button>

     <!-- Full Sidebar Menu Close Button -->
     <button class="button-close-fullsidebar">
         <i class="ti ti-x align-middle"></i>
     </button>

     <div data-simplebar>

         <!--- Sidenav Menu -->
         <ul class="side-nav">
             @forelse(user()->groups as $group)

             <li class="side-nav-item">
                 <a href="{{route('groups.index', $group->slug)}}" class="side-nav-link">
                     <span class="menu-icon"><img src="{{asset($group->logo ?? 'assets/img/group.jpg') }}" class="rounded-circle me-lg-2 d-flex object-fit-cover" width="20" height="20" alt="{{$group->title}}"></span>
                     <span class="menu-text">{{$group->title}}</span>
                 </a>
             </li>
             @empty
             <div class="d-flex justify-content-center align-items-center">
                 <div class="d-flex flex-column">
                     <div class="my-2 side-nav-title"> هیچ گروهی یافت نشد.</div>
                     <a href="{{ route('groups.create') }}" class="btn btn-primary btn-sm mx-auto">ایجاد</a>
                 </div>
             </div>
             @endforelse
         </ul>

         <div class="clearfix"></div>
     </div>
 </div>
 <!-- Sidenav Menu End -->