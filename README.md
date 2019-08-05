# eida-docker-wf-guis

Web interface for browsing seismic data availability and
quality metrics provided by a WFCatalog service.

### Build and run container

The following commands will build and run a web interface container running on
port 3000.
It may be necessary to use `sudo` to run Docker commands.

```bash
docker build -t wf:latest .
```

```bash
docker run -p 3000:3000 --name wf \
  wf:latest
```

### Alternative data service urls

URLs for WF Catalog and FDSNWS services can be overridden during container
startup by setting environment variables.  The default values correspond to the
_resif.fr_ service.

```bash
docker build -t wf:latest .
docker run -p 3000:3000 --name wf \
  -e "WFCATALOG_ADDRESS=http://ws.resif.fr/eidaws/wfcatalog/1/query" \
  -e "FDSNWS_ADDRESS=http://ws.resif.fr/fdsnws/station/1/query" \
  wf:latest
```
