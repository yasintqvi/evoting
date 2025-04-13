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
             @can(\App\Enums\Permission::LIST_COMPANIES->value)
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

             @can(\App\Enums\Permission::CREATE_COMPANY->value)
             <div class="d-flex flex-column mt-3">
                 <a href="{{ route('companies.create') }}" class="btn btn-primary btn-sm mx-auto">افزودن شرکت</a>
             </div>
             @endcan
             @endcan

             <li class="side-nav-title">مدیریت</li>

             @can(\App\Enums\Permission::VIEW_USERS->value)
             <li class="side-nav-item">
                 <a href="{{route('users.index')}}" class="side-nav-link">
                     <span class="menu-icon">
                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                             <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z" />
                         </svg>
                     </span>
                     <span class="menu-text"> مدیریت کاربران </span>
                 </a>
             </li>
             @endcan

             @can(\App\Enums\Permission::VIEW_ROLES->value)
             <li class="side-nav-item">
                 <a href="{{route('roles.index')}}" class="side-nav-link">
                     <span class="menu-icon">
                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16">
                             <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z" />
                             <path d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415z" />
                         </svg>
                     </span>
                     <span class="menu-text"> مدیریت دسترسی ها </span>
                 </a>
             </li>
             @endcan

             @can(\App\Enums\Permission::VIEW_USERS->value)
             <li class="side-nav-item">
                 <a href="{{ route('users.activities.index') }}" class="side-nav-link">
                     <span class="menu-icon">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clipboard-text">
                             <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                             <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                             <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                             <path d="M9 12h6" />
                             <path d="M9 16h6" />
                         </svg>
                     </span>
                     <span class="menu-text"> لاگ فعالیت کاربران </span>
                 </a>
             </li>
             @endcan
         </ul>
         


         <div class="clearfix"></div>
       
     </div>
 </div>
 <!-- Sidenav Menu End -->