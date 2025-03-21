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
             <li class="side-nav-item">
                 <a href="{{route('elections.index', $company->slug)}}" class="side-nav-link">
                     <span class="menu-icon">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                             <path fill="currentColor" d="m17.578 4.432l-2-1.05C13.822 2.461 12.944 2 12 2s-1.822.46-3.578 1.382l-.321.169l8.923 5.099l4.016-2.01c-.646-.732-1.688-1.279-3.462-2.21m4.17 3.534l-3.998 2V13a.75.75 0 0 1-1.5 0v-2.286l-3.5 1.75v9.44c.718-.179 1.535-.607 2.828-1.286l2-1.05c2.151-1.129 3.227-1.693 3.825-2.708c.597-1.014.597-2.277.597-4.8v-.117c0-1.893 0-3.076-.252-3.978M11.25 21.904v-9.44l-8.998-4.5C2 8.866 2 10.05 2 11.941v.117c0 2.525 0 3.788.597 4.802c.598 1.015 1.674 1.58 3.825 2.709l2 1.049c1.293.679 2.11 1.107 2.828 1.286M2.96 6.641l9.04 4.52l3.411-1.705l-8.886-5.078l-.103.054c-1.773.93-2.816 1.477-3.462 2.21" />
                         </svg>
                     </span>
                     <span class="menu-text"> انتخابات </span>
                 </a>
             </li>
             <li class="side-nav-item">
                 <a href="#" class="side-nav-link">
                     <span class="menu-icon">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                             <path fill="currentColor" fill-rule="evenodd" d="M8.048 2.488a.75.75 0 0 1-.036 1.06l-4.286 4a.75.75 0 0 1-1.095-.076l-1.214-1.5a.75.75 0 0 1 1.166-.944l.708.875l3.697-3.451a.75.75 0 0 1 1.06.036M11.25 5a.75.75 0 0 1 .75-.75h10a.75.75 0 0 1 0 1.5H12a.75.75 0 0 1-.75-.75M8.048 9.488a.75.75 0 0 1-.036 1.06l-4.286 4a.75.75 0 0 1-1.095-.076l-1.214-1.5a.75.75 0 1 1 1.166-.944l.708.875l3.697-3.451a.75.75 0 0 1 1.06.036M11.25 12a.75.75 0 0 1 .75-.75h10a.75.75 0 0 1 0 1.5H12a.75.75 0 0 1-.75-.75m-3.202 4.488a.75.75 0 0 1-.036 1.06l-4.286 4a.75.75 0 0 1-1.095-.076l-1.214-1.5a.75.75 0 1 1 1.166-.944l.708.875l3.697-3.451a.75.75 0 0 1 1.06.036M11.25 19a.75.75 0 0 1 .75-.75h10a.75.75 0 0 1 0 1.5H12a.75.75 0 0 1-.75-.75" clip-rule="evenodd" />
                         </svg>
                     </span>
                     <span class="menu-text"> نظرسنجی </span>
                 </a>
             </li>
             <li class="side-nav-item">
                 <a href="{{ route('company.users.index' , $company->slug) }}" class="side-nav-link">
                     <span class="menu-icon">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                             <circle cx="15" cy="6" r="3" fill="currentColor" opacity="0.4" />
                             <ellipse cx="16" cy="17" fill="currentColor" opacity="0.4" rx="5" ry="3" />
                             <circle cx="9.001" cy="6" r="4" fill="currentColor" />
                             <ellipse cx="9.001" cy="17.001" fill="currentColor" rx="7" ry="4" />
                         </svg>
                     </span>
                     <span class="menu-text"> اعضا </span>
                 </a>
             </li>
         </ul>

         <div class="clearfix"></div>
     </div>
 </div>
 <!-- Sidenav Menu End -->