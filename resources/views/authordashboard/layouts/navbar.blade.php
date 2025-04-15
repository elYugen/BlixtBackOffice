<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0a549a;">
    <div class="container-fluid">
      <a class="navbar-brand text-white d-flex align-center gap-2" href="{{ route('authordashboard.index')}}">
        Blixt - Espace Auteur</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('authordashboard.index') ? 'active' : '' }}" 
               href="{{ route('authordashboard.index')}}">
              <i class="bi bi-speedometer2"></i> Tableau de bord
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('authordashboard.novels*') ? 'active' : '' }}" 
               href="{{ route('authordashboard.novels')}}">
              <i class="bi bi-book"></i> Mes Romans
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('authordashboard.comics*') ? 'active' : '' }}" 
               href="{{ route('authordashboard.comics')}}">
               <i class="bi bi-grid"></i> Mes Comics
            </a>
          </li>
        </ul>
  
        @if (Auth::check())
        <div class="d-flex align-items-center ms-auto">
          <a href="{{ route('authordashboard.user.index') }}" class="d-flex align-items-center text-decoration-none">
            <img src="{{ Auth::user()->author && Auth::user()->author->avatar ? asset('storage/' . Auth::user()->author->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                 class="rounded-circle me-2" 
                 alt="Profile Picture" 
                 style="width: 30px; height: 30px; object-fit: cover;">
            <span class="me-3 my-2 my-lg-0 text-white">{{ Auth::user()->name }} ({{ Auth::user()->author->pen_name }})</span>
          </a>
          <form class="d-flex my-2 my-lg-0" action="{{ route('authordashboard.logout') }}" method="POST">
            @csrf
            <button class="btn btn-outline-light border-0" type="submit">
                <i class="bi bi-box-arrow-right"></i>
            </button>
          </form>
        </div>
        @endif 
      </div>
    </div>
  </nav>

  @if(request()->routeIs('authordashboard.novels*') || request()->routeIs('authordashboard.novels.chapters*'))
  <nav class="navbar navbar-expand-lg py-0" style="background-color: #f8f9fa; box-shadow: 0 1px 2px rgba(0,0,0,.1);">
      <div class="container-fluid">
          <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav">
                  <li class="nav-item">
                      <a class="nav-link py-2 {{ request()->routeIs('authordashboard.novels') ? 'active fw-bold' : '' }}" 
                        href="{{ route('authordashboard.novels') }}" style="color: #0a549a; font-size: 0.9rem;">
                        <i class="bi bi-list-ul"></i> Liste des romans
                      </a>
                  </li>
                  <li class="nav-item d-flex align-items-center">
                    <span style="color: #0a549a; opacity: 0.4;">/</span>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link py-2 {{ request()->routeIs('authordashboard.novels.create') ? 'active fw-bold' : '' }}" 
                       href="{{ route('authordashboard.novels.create') }}" style="color: #0a549a; font-size: 0.9rem;">
                        <i class="bi bi-book"></i> Créer un nouveau roman
                    </a>
                  </li>
                  <li class="nav-item d-flex align-items-center">
                    <span style="color: #0a549a; opacity: 0.4;">/</span>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link py-2 {{ request()->routeIs('authordashboard.novels.chapters.create') ? 'active fw-bold' : '' }}" 
                      href="{{ route('authordashboard.novels.chapters.create') }}" style="color: #0a549a; font-size: 0.9rem;">
                      <i class="bi bi-file-earmark"></i> Créer un nouveau chapitre
                    </a>
                </li>
              </ul>
          </div>
      </div>
  </nav>
  @endif

  @if(request()->routeIs('authordashboard.comics*') || request()->routeIs('authordashboard.comics.chapters*'))
  <nav class="navbar navbar-expand-lg py-0" style="background-color: #f8f9fa; box-shadow: 0 1px 2px rgba(0,0,0,.1);">
      <div class="container-fluid">
          <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav">
                  <li class="nav-item">
                      <a class="nav-link py-2 {{ request()->routeIs('authordashboard.comics') ? 'active fw-bold' : '' }}" 
                        href="{{ route('authordashboard.comics') }}" style="color: #0a549a; font-size: 0.9rem;">
                        <i class="bi bi-list-ul"></i> Liste des bandes dessinées
                      </a>
                  </li>
                  <li class="nav-item d-flex align-items-center">
                    <span style="color: #0a549a; opacity: 0.4;">/</span>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link py-2 {{ request()->routeIs('authordashboard.comics.create') ? 'active fw-bold' : '' }}" 
                       href="{{ route('authordashboard.comics.create') }}" style="color: #0a549a; font-size: 0.9rem;">
                        <i class="bi bi-book"></i> Créer une nouvelle bande dessinée
                    </a>
                  </li>
                  <li class="nav-item d-flex align-items-center">
                    <span style="color: #0a549a; opacity: 0.4;">/</span>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link py-2 {{ request()->routeIs('authordashboard.novels.chapters.create') ? 'active fw-bold' : '' }}" 
                      href="{{ route('authordashboard.comics.chapters.create') }}" style="color: #0a549a; font-size: 0.9rem;">
                      <i class="bi bi-file-earmark"></i> Créer un nouveau chapitre
                    </a>
                </li>
              </ul>
          </div>
      </div>
  </nav>
  @endif