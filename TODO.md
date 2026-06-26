# DBF Manager — TODO

## Sucursales con CO_OFES.DBF

Las siguientes rutas en `/var/smb/` contienen `CO_OFES.DBF`.
Algunas tienen el archivo directamente en la raíz, otras dentro de una subcarpeta.

| Sucursal | Ruta | Dueño | Tamaño |
|----------|------|-------|--------|
| almar | `/var/smb/almar/ALMAR/CO_OFES.DBF` | hannia:canovas | ~48 MB |
| cvini | `/var/smb/cvini/CVINI/CO_OFES.DBF` | jhony:canovas | ~44 MB |
| gcedi | `/var/smb/gcedi/CO_OFES.DBF` | root:canovas | ~48 MB |
| gcedi | `/var/smb/gcedi/GCEDI/CO_OFES.DBF` | victor:canovas | ~48 MB |
| gchcv | `/var/smb/gchcv/GCHCV/CO_OFES.DBF` | victor:canovas | ~48 MB |
| hcxve | `/var/smb/hcxve/HCXVE/CO_OFES.DBF` | sandra:canovas | ~48 MB |
| mcedv | `/var/smb/mcedv/MCEDV/CO_OFES.DBF` | sandra:canovas | ~48 MB |
| pcedv | `/var/smb/pcedv/CO_OFES.DBF` | root:canovas | ~48 MB |
| vcedv | `/var/smb/vcedv/VCEDV/CO_OFES.DBF` | rosalba:canovas | ~48 MB |

Nota: `gcedi` tiene dos archivos (raíz + subcarpeta GCEDI).

## Pendientes

### Cambio de clave
- Botón en perfil/usuario
- Generar contraseña aleatoria imprimible
- Mostrar una vez con advertencia "no se podrá recuperar"
- Confirmar para guardar hash

### Rutas por plaza
- CRUD de plazas (nombre + rutas destino)
- Al subir, seleccionar plaza
- Las rutas de la plaza determinan los destinos de copia
- Tabla `plazas` e `plaza_rutas` en BD

### MD5 duplicate warning
- Antes de procesar upload, consultar si el hash ya existe en logs_carga
- Mostrar advertencia al usuario
- Opción de continuar o cancelar

## Dudas
- ¿CVINI tiene menor tamaño (44 MB) por diferencia real o archivo distinto?
- ¿gcedi debe copiar a raíz, a GCEDI, o ambas?
