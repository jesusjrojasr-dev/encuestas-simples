# 📊 Encuestas Simples

**Encuestas Simples** es un plugin para WordPress que permite crear encuestas simples de dos opciones, mostrar los resultados en tiempo real y exportarlos en formato CSV desde el panel de administración.

---

## 📝 Descripción

Este plugin permite a los administradores del sitio:
- Crear encuestas personalizadas con **dos opciones** de respuesta.
- Mostrar los resultados de las votaciones en el backend y frontend.
- Limitar la votación a una vez por usuario mediante **cookies**.
- Exportar un informe consolidado de todas las encuestas y sus resultados en formato **CSV**.

---

## 🚀 Características Principales

1. **Custom Post Type**: Administra las encuestas como un tipo de contenido independiente en WordPress.
2. **Metabox en el Editor**:  
   - Configura las dos opciones de la encuesta.  
   - Obtén el **shortcode** para incrustar la encuesta en cualquier página o entrada.  
   - Visualiza el **conteo de votos** por opción y el total.
3. **Shortcode para Frontend**:  
   - Muestra la encuesta con botones interactivos.
   - Muestra un mensaje de agradecimiento con los resultados tras votar.  
4. **Control de Votos**:  
   - El usuario solo puede votar una vez, utilizando **cookies**.
5. **Exportación de Resultados**:  
   - Exporta un reporte consolidado de todas las encuestas y resultados desde el backend en formato CSV.  

---

## 📥 Instalación

1. **Descarga el Plugin**:  
   - Descarga el archivo ZIP del plugin desde [GitHub](#).

2. **Instala en WordPress**:  
   - Ve a **Plugins > Añadir nuevo > Subir Plugin**.  
   - Sube el archivo ZIP y actívalo.

3. **Crea una Encuesta**:  
   - En el menú de WordPress, accede a **Encuestas**.  
   - Crea una nueva encuesta y configura las dos opciones en el metabox.  
   - Copia el shortcode generado para incrustarlo en una página.

---

## 🛠️ Uso del Shortcode

Utiliza el siguiente shortcode para mostrar la encuesta en cualquier parte de tu sitio:

[encuesta id="XX"]

- Reemplaza `XX` con el **ID** de la encuesta.

---

## 🎮 Funcionalidad en el Frontend

1. Los usuarios verán **dos botones** correspondientes a las opciones configuradas.
2. Al votar, se mostrará un mensaje de agradecimiento y los resultados actualizados.
3. El usuario no podrá votar nuevamente (validación mediante cookies).

---

## 📊 Exportación de Resultados

1. En el menú de administración, accede a **Encuestas > Reportes**.
2. Haz clic en el botón **Exportar Reporte Completo**.
3. Se generará un archivo CSV con las siguientes columnas:
   - **Encuesta ID**  
   - **Título Encuesta**  
   - **Opción 1**  
   - **Votos Opción 1**  
   - **Opción 2**  
   - **Votos Opción 2**  
   - **Total de Votos**

---

## 🖥️ Capturas de Pantalla

1. **Metabox en el Editor**  
   - Configuración de opciones y visualización de resultados.

2. **Frontend de la Encuesta**  
   - Botones interactivos y visualización de resultados.

3. **Exportación en el Backend**  
   - Botón para descargar el reporte CSV consolidado.

---

## 🛡️ Seguridad

- **Votación Controlada**:  
   - Validación mediante **cookies** para evitar votos duplicados.  
- **Saneamiento de Datos**:  
   - Todas las entradas y salidas de datos utilizan funciones de WordPress para garantizar la seguridad.

---

## 👨‍💻 Contribuciones

¡Las contribuciones son bienvenidas! Si tienes ideas para mejorar el plugin, sigue estos pasos:

1. Haz un **fork** de este repositorio.
2. Crea una nueva rama:  
   git checkout -b feature/nueva-funcionalidad
3. Realiza los cambios y haz un commit:
   git commit -m "Agrega nueva funcionalidad"

---

## 📄 Licencia

Este proyecto está bajo la licencia **MIT**. Puedes usarlo libremente en tus proyectos.

---

## 💬 Soporte

Si tienes preguntas o problemas con el plugin, no dudes en abrir un **Issue** en este repositorio o contactarme a través del [correo](mailto:jesusjrojasr1989@gmail.com).
