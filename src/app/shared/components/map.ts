import {Component, OnInit} from "@angular/core";
import {ActivatedRoute, Params} from "@angular/router";
import {Status} from "../classes/status";
import {Observable} from "rxjs";
import "rxjs/add/observable/from";
import "rxjs/add/operator/switchMap";
import 'rxjs/add/observable/of';
import {ArtService} from "../services/art.service";
import {Art} from "../classes/art";



//we had art.component.html why?
@Component({
    template: require("./map.component.html"),
    selector: "map"
})

export class MapComponent implements OnInit {

    // empty array of lat/long points
    public positions: any = [];


    //Rochelle just has an empty constructor...??
    constructor(protected artService : ArtService) {}

    ngOnInit() : void {

        // OnInit, make call to populate positions array thru the Observable
        this.showMarkersFromObservable();
    }

    // This is a dummy example. This just creates random points for the map.
    // Your points will come from your Hub Service
    getArtMarkers() : any {
        let artLat: number, artLong: number;

        let positions = [];


        artLat = art.artLat;
        artLong = art.artLong;
        positions.push(artLat, artLong);


        // for (let i = 0 ; i < 9; i++) {
        //     randomLat = Math.random() * (43.7399 - 43.7300) + 43.7300;
        //     randomLng = Math.random() * (-79.7600 - -79.7699) + -79.7699;
        //     positions.push([randomLat, randomLng]);
        // }




        return positions;
    }

    // this is an example that uses an Observable - much like
    // the call to your service. This works, and is called OnInit,
    // and when the button is pushed too.
    showMarkersFromObservable() {
        Observable.of(this.getArtMarkers()) // Think this as http call
            .subscribe( positions => {
                this.positions = positions;
            });
    }

}