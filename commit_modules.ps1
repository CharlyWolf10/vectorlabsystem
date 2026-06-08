# Módulo Inventario
git checkout modulo-inventario
git add app/Livewire/Inventario.php app/Models/Producto.php app/Models/Categoria.php
git add database/migrations/*categoria*.php
git add database/migrations/*productos*.php
git add resources/views/livewire/inventario.blade.php public/css/inventario.css public/js/inventario.js
git commit -m "feat(inventario): Mejoras SKU, categorias y soft deletes"

# Módulo Cotizador
git checkout -b modulo-cotizador
git add app/Livewire/Cotizador.php resources/views/livewire/cotizador.blade.php public/js/cotizador.js "resources/views/components/*cotizador.blade.php"
git commit -m "feat(cotizador): Implementacion inicial de cotizador"

# Módulo Gastos Operativos
git checkout -b modulo-gastos
git add app/Livewire/GastosOperativos.php app/Models/GastoOperativo.php resources/views/livewire/gastos-operativos.blade.php public/js/gastos-operativos.js "resources/views/components/*gastos-operativos.blade.php" database/migrations/*gasto*.php
git commit -m "feat(gastos): Implementacion inicial de gastos operativos"

# Módulo Formulas / Producción
git checkout -b modulo-formulas
git add app/Models/Formula.php app/Models/FormulaIngrediente.php database/migrations/*formula*.php
git commit -m "feat(formulas): Implementacion inicial de formulas"

# Módulo Compras
git checkout modulo-compras
git add resources/views/livewire/compras-y-pagos.blade.php public/js/compras-y-pagos.js
git commit -m "feat(compras): Actualizacion modulo compras"

# Módulo Arqueos
git checkout modulo-arqueos
git add resources/views/livewire/arqueos.blade.php public/js/arqueos.js
git commit -m "feat(arqueos): Actualizacion modulo arqueos"

# Módulo Clientes
git checkout modulo-clientes
git add resources/views/livewire/clientes.blade.php public/js/clientes.js
git commit -m "feat(clientes): Actualizacion modulo clientes"

# Módulo Usuarios
git checkout modulo-usuarios
git add resources/views/livewire/usuarios.blade.php public/js/usuarios.js
git commit -m "feat(usuarios): Actualizacion modulo usuarios"

# Módulo POS
git checkout modulo-pos
git add resources/views/livewire/punto-de-venta.blade.php public/js/punto-de-venta.js
git commit -m "feat(pos): Actualizacion modulo pos"

# Core Updates (Routes, Layouts)
git checkout -b core-updates
git add routes/web.php resources/views/layouts/app.blade.php resources/views/layouts/navigation.blade.php resources/views/auth/login.blade.php public/js/login.js
git commit -m "chore(core): Actualizacion de rutas y navegacion global"

# Volver a main
git checkout main
