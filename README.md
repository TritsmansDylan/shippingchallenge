2APPAI Shipping Challenge

The assignment

? Create your own kubernetes stack with 1 worker
? Containerized application for worker
		? When the surname changes in DB, it changes for the webpage 			     automatically.
		? When the layout changes, the worker will display the new layout                   		     automatically
		? Use the webstack which is assigned to you

My webstack

Webserver
Database
Script Language
Extra
Lighttpd
MariaDB
PHP
Rancher

How to earn points

	? 0/20 – Be a plant
          ?10/20 – stack in Docker
          ? 14/20 – mk8s cluster with 1 worker

Extra points
 
	? Vagrant
	? Extra worker
	? Management webplatform for containers
	? Something else than mk8s with the same purpose
	? Be a funny guy
 




My setup

Dockerfile

FROM ubuntu:latest

#timezone
ENV TZ=Europe/Brussels
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

#updates
RUN apt-get update && apt-get upgrade -y

#install lighttpd, php, enable cgi
RUN apt-get install -y \
            lighttpd \
            php \
            php-cgi \
            php-mysql 
RUN lighty-enable-mod fastcgi \
    && lighty-enable-mod fastcgi-php

#reload lighttpd
RUN service lighttpd restart

#copy test files
COPY test.php dbconnection.php dbfetch.php /var/www/html/

RUN chmod +x /var/www/html/test.php \
             /var/www/html/dbconnection.php \
             /var/www/html/dbfetch.php \
          && service lighttpd restart

#open port
EXPOSE 80

#run lighttpd server
CMD ["lighttpd", "-D", "-f","/etc/lighttpd/lighttpd.conf"]











Steps

 ? Get the latest stable Ubuntu
 ?Set the timezone(precaution just in case, heard some webservices hate being in the                       wrong time zone) 
? Update and upgrade everything
? Install the essential software for my stack
? Copy all the files needed
? Make them executable
? Open up port 80
? run the lighttpd server

Kubernetes

I put all three of the needed parts (deployment, service and ingress) in one file for ease of use. Here are all three parts separated.

Deployment

This downloads the image and creates 3 pods using the dockerfile. It also checks for the newest version on start.

apiVersion: apps/v1
kind: Deployment
metadata:
   name:shippingchallenge-lighttpd
spec:
  replicas: 3
  selector:
    matchLabels:
      app: lighttpd
  template:
    metadata:
         labels:
            app: lighttpd
  spec:
    containers:
      - name: lighttpd
        image: tritsmansdylan/shippingchallenge
        imagePullPolicy: Always
        ports:
          - containerPort: 80
            name: lighttpd





Service

This makes sure the pods run on the correct port.

apiVersion: v1
kind: Service
metadata:
  name: shippingchallenge-service
  labels:
    app: lighttpd
spec:
  type: ClusterIP
  ports:
    - port: 80
  selector:
    app: lighttpd

Ingress

This is used to balance the load between the pods and make the application available to other devices.

apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: shippingchallenge-ingress
  namespace: default
spec:
  rules:
    - host: shipping-challenge.local
      http:
        paths:
          - path: /
            pathtype: Prefix
            backend:
              service:
                name: shippingchallenge-service
                port:
                  number: 80

Dockerhub

I host my shipping challenge image on Dockerhub:
https://hub.docker.com/repository/docker/tritsmansdylan/shippingchallenge/

I have linked my github to dockerhub, when a git push is made this will automatically be built via automated builds in dockerhub.


Installation

Minikube

? install Kubectl  https://kubernetes.io/docs/tasks/tools/install-kubectl/
? install minikkube https://minikube.sigs.k8s.io/docs/start/

? Fire up Minikube with its add-on ingress.
	minikube start
	minikube addons enable ingress
Shipping challenge
? install mariadb using these commands:
	$ sudo apt-get install software-properties-common
	
	$ sudo apt-key adv –fetch-keys 'https://mariadb.org/mariadb_release_signing_key.asc'

	$ sudo add-apt-repository 'deb [arch=amd64,arm64,ppc64el] 	http://mirrors.piconets.webwerks.in/mariadb-mirror/repo/10.5/ubuntu focal main'

	$ sudo apt update

	$ sudo apt install mariadb-server
? secure installation with the script provided using:
	$ sudo mysql_secure_installation
? give yourself access to the database by following these steps:
	? find the database config file and add this to the [mysqld] tag:
		port = 3306
 		bind_address = 0.0.0.0
 		skip_name_resolve    
	? log in to the database on the machine running it and use this command:
		GRANT ALL ON database_name.* TO ‘your_username’@’%’ IDENTIFIED BY 		‘your_password’;
? apply the yaml files on the machine running minikube with:
		kubectl apply -f deployment.yaml
		kubectl apply -f service.yaml
		kubectl apply-f ingress.yaml
? get the ingress ip address:
		kubectl get ingress
? add this ip to your host file:
	example: 192.168.99.101	shippingchallenge.local
	

