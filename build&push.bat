@ECHO OFF
docker build . -t omeka_s_retopea
docker tag omeka_s_retopea registry.docker.libis.be/omeka_s_retopea
docker push registry.docker.libis.be/omeka_s_retopea
ECHO Image built, tagged and pushed successfully
PAUSE
