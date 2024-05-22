FROM bitnami/prestashop

USER 0

RUN install_packages vim

RUN chmod -R 777 /opt/bitnami/prestashop

COPY ./prestashop/img/ /opt/bitnami/prestashop/img/
COPY ./prestashop/modules/ /opt/bitnami/prestashop/modules/
COPY ./prestashop/themes/ /opt/bitnami/prestashop/themes/

