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

CMD ["npm", "start"]
