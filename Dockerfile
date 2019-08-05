FROM centos:latest

RUN yum -y update ; \ 
    yum -y install git wget epel-release ; \
    yum -y install nodejs npm ; \
    yum -y install http://dl.fedoraproject.org/pub/epel/testing/7/x86_64/Packages/b/bootswatch-fonts-3.3.5.3-2.el7.noarch.rpm ;\
    yum clean all ;

RUN npm install


RUN mkdir /app/
COPY code /app/
WORKDIR /app/
RUN ln -s /usr/share/fonts/bootswatch/ fonts

EXPOSE 3000 

# Set default external URLs
ENV WFCATALOG_ADDRESS "http://ws.resif.fr/eidaws/wfcatalog/1/query"
ENV FDSNWS_ADDRESS "http://ws.resif.fr/fdsnws/station/1/query"

# Dynamically rewrite external URLs based on environment variables
# Note that double quotes in sed expression must be escaped e.g. \"
CMD \
  sed -i -E "s|(var WFCATALOG_ADDRESS =).*|\1 \"${WFCATALOG_ADDRESS}\"|g" /app/availability/js/interface.js && \
  sed -i -E "s|(var WFCATALOG_ADDRESS =).*|\1 \"${WFCATALOG_ADDRESS}\"|g" /app/metrics/js/interface.js && \
  sed -i -E "s|(var FDSNWS_ADDRESS =).*|\1 \"${FDSNWS_ADDRESS}\"|g" /app/availability/js/interface.js && \
  sed -i -E "s|(var FDSNWS_ADDRESS =).*|\1 \"${FDSNWS_ADDRESS}\"|g" /app/metrics/js/interface.js && \
  npm start;
