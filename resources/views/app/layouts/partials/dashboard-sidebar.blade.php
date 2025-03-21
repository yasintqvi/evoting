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
         <ul class="side-nav mt-4">
             <li class="side-nav-title">شرکت ها</li>

             @forelse(user()->companies as $company)
             <li class="side-nav-item">
                 <a href="{{route('companies.index', $company->slug)}}" class="side-nav-link">
                     <span class="menu-icon"><img src="{{asset($company->logo ?? 'assets/img/company.jpg') }}" class="rounded-circle me-lg-2 d-flex object-fit-cover" width="20" height="20" alt="{{$company->title}}"></span>
                     <span class="menu-text">{{$company->title}}</span>
                 </a>
             </li>
             @empty
             <div class="d-flex justify-content-center align-items-center">
                 <div class="d-flex flex-column">
                     <div class="mt-2 side-nav-title"> هیچ شرکتی یافت نشد.</div>
                 </div>
             </div>
             @endforelse

             <div class="d-flex flex-column mt-3">
                 <a href="{{ route('companies.create') }}" class="btn btn-primary btn-sm mx-auto">افزودن شرکت</a>
             </div>

             <li class="side-nav-title">مدیریت</li>

             <li class="side-nav-item">
                 <a href="{{route('users.index')}}" class="side-nav-link">
                     <span class="menu-icon">
                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                             <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z" />
                         </svg>
                     </span>
                     <span class="menu-text"> مدیرت اعضا </span>
                 </a>
             </li>
         </ul>



         <div class="clearfix"></div>
     </div>
 </div>
 <!-- Sidenav Menu End -->