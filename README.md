# Menu Management System

A Laravel-powered web application for creating, organizing, and publishing digital menu content to any number of display screens.  It provides an easy-to-use, dark-themed editor built with Livewire Volt and FluxUI.

## Features
- Hierarchical entities: **Screens → Collections → Columns → Items**
- Responsive, dark-themed UI based on **Tailwind CSS** & **FluxUI**
- Real-time CRUD operations with **Laravel Livewire Volt** modals
- Fully keyboard-navigable sidebar with collapsible sections
- Runs locally at `https://menu.test` out-of-the-box (Laragon/Vite)

## Tech Stack
| Layer            | Technology |
|------------------|------------|
| Backend          | Laravel 11, PHP ≥8.2 |
| Realtime UI      | Livewire Volt |
| UI Components    | FluxUI (Tailwind plugin) |
| Styling / Build  | Tailwind CSS, Vite |
| Database         | MySQL / MariaDB (works with any Laravel-supported DB) |

## Installation
1. **Clone & prepare**
   ```bash
   git clone https://github.com/SilverPaladin/Menu.git
   cd menu
   cp .env.example .env        # adjust DB credentials, or use sqlite with default settings
   ```
2. **Install PHP dependencies**
   ```bash
   composer install --prefer-dist --no-dev
   php artisan key:generate
   ```
3. **Install Node dependencies & compile assets**
   ```bash
   npm install
   npm run build   # or `npm run dev` for hot-reload
   ```
4. **Run database migrations & seeders**
   ```bash
   php artisan migrate --seed
   ```
5. **Serve the application**
   ```bash
   php artisan serve   # http://127.0.0.1:8000
   ```
   – or point your local DevStack (Laragon/Valet) at `public/` and add the host `menu.test` to your hosts file.

## Usage
Create account, log in, and start adding **Screens**, then **Collections**, **Columns**, and **Items**. Content appears instantly without page refresh thanks to Livewire.

## Contributing
Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

## License
This project is released under the **Creative Commons Attribution-NonCommercial 4.0 International** license (CC BY-NC 4.0).

> You may use, modify, and redistribute the code **for non-commercial purposes only**, provided that you give appropriate credit.  Commercial use requires a separate commercial license — please contact the maintainers.
